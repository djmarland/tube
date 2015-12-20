(function() {
    "use strict";
    // @todo - split this stuff into ES6 classes/modules
    var TUBE = function() {
        this.currentPage = null; // unknown until the JS sends you somewhere
        this.statusData = null;
        this.lineTemplate = null;
        this.homeTemplate = null;
        this.settingsTemplate = null;
        this.mainArea = document.getElementById(this.mainPageID);
        this.init();
    };

    TUBE.prototype = {
        mainPageID : 'main-body',
        bodyView : {
            home : 'homepage',
            line : 'line',
            settings : 'settings'
        },
        selectors : {
            mainPage : '[data-js="main-body"]'
        },
        paths : {
            data : '/all.json',
            home : '/',
            line : '/bakerloo-line',
            settings : '/settings'
        },
        storage : {
            allLines : 'allLines'
        },
        safeResponse : function(request) {
            return (request.status >= 200 && request.status < 400);
        },
        ajax : function(url, callback) {
            var request = new XMLHttpRequest();
            request.open('GET', url, true);

            request.onload = function() {
                if (this.safeResponse(request)) {
                    callback(request.response);
                }
            }.bind(this);
            request.send();
        },
        init : function() {
            this.refreshData();
            this.getTemplates();
            this.addListeners();
        },
        stringToDom : function(string) {
            var doc = document.createElement('div');
            doc.innerHTML = string;
            return doc;
        },
        getTemplates : function() {
            var body = document.body,
                currentPage = document.getElementById(this.mainPageID);
            // home
            if (body.dataset.view == this.bodyView.home) {
                // home was here. use it
                this.homeTemplate = currentPage;
            } else {
                // go and fetch it
                this.ajax(this.paths.home, function(response) {
                    this.homeTemplate = this.stringToDom(response).querySelector(this.selectors.mainPage);
                }.bind(this));
            }

            // line
            if (body.dataset.view == this.bodyView.line) {
                // home was here. use it
                this.lineTemplate = currentPage;
            } else {
                // go and fetch it
                this.ajax(this.paths.line, function(response) {
                    this.lineTemplate = this.stringToDom(response).querySelector(this.selectors.mainPage);
                }.bind(this));
            }

            // settings
            if (body.dataset.view == this.bodyView.settings) {
                // home was here. use it
                this.settingsTemplate = currentPage;
            } else {
                // go and fetch it
                this.ajax(this.paths.settings, function(response) {
                    this.settingsTemplate = this.stringToDom(response).querySelector(this.selectors.mainPage);
                }.bind(this));
            }
        },
        refreshData : function() {
            this.ajax(this.paths.data, function(response) {
                window.localStorage.setItem(this.storage.allLines, response);
            }.bind(this));
        },
        swapBody : function(data) {
            this.mainArea.innerHTML = data.innerHTML;
        },
        goHome : function() {
            var path = '/';
            if (!this.homeTemplate) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.homeTemplate);
            document.body.dataset.view = this.bodyView.home;
            window.history.pushState({}, '', path); // @todo - sort titles
        },
        goToSettings : function() {
            var path = '/settings';
            if (!this.settingsTemplate) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.settingsTemplate);
            document.body.dataset.view = this.bodyView.settings;
            window.history.pushState({}, '', path); // @todo - sort titles
        },
        goToLine : function(lineKey) {
            var path = '/' + lineKey,
                lineData = this.getLineData(lineKey);
            if (!this.lineTemplate || !lineData) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.transformLineTemplate(this.lineTemplate, lineData));
            document.body.dataset.view = this.bodyView.line;
            window.history.pushState({}, '', path); // @todo - sort titles
        },
        transformLineTemplate : function(template, data) {
            var page = this.stringToDom(template.innerHTML),
                title = page.querySelector('[data-js="line-title"]'),
                statusPanel = page.querySelector('[data-js="line-status-panel"]'),
                updatedPanel = page.querySelector('[data-js="line-updated"]'),
                panelContent = '<h2>No information</h2>',
                updatedContent = 'N/A';

            title.dataset.linebox = data.urlKey;
            title.innerHTML = data.name;
            if (data.latestStatus) {
                panelContent = '<h2 class="g-unit">' + data.latestStatus.title + '</h2>';
                if (
                    data.latestStatus.isDisrupted &&
                    data.latestStatus.descriptions
                ) {
                    data.latestStatus.descriptions.forEach(function(description){
                       panelContent += '<p class="g-unit">' + description + '</p>';
                    });
                }
                updatedContent = data.latestStatus.updatedAtFormatted;
            }
            statusPanel.innerHTML = panelContent;
            updatedPanel.innerHTML = updatedContent;

            return page;
        },
        getLineData : function(key) {
            var saved = window.localStorage.getItem('allLines'),
                data = null,
                allLines;
            if (!saved) {
                return null;
            }
            allLines = JSON.parse(saved);
            if (!allLines || !allLines.lines) {
                return null;
            }
            allLines.lines.forEach(function(l) {
                if (l.urlKey == key) {
                    data = l;
                }
            });
            return data;
        },
        navigate : function(path) {
            // a very simple router
            if (path == '/') {
                return this.goHome();
            }
            if (path == '/settings') {
                return this.goToSettings();
            }
            path = path.replace('/','');
            return this.goToLine(path);
        },
        addListeners : function() {
            // delegated listener for all <a> elements
            document.body.addEventListener('click', function(event) {
                var element = event.target;
                while (
                    element != document.body &&
                    element.tagName.toLowerCase() != 'a'
                ) {
                    element = element.parentNode;
                }
                if (element.hostname && element.hostname == window.location.hostname) {
                    event.preventDefault();
                    this.navigate(element.pathname);
                }
            }.bind(this));

            window.onpopstate = function () {
                this.navigate(document.location.pathname);
            }.bind(this);
        }
    };


    //
    //function loadData()
    //{
    //    var saved = window.localStorage.getItem('allLines');
    //    if (saved) {
    //        return JSON.parse(saved);
    //    }
    //    return null;
    //}
    //
    //function getLineData(key)
    //{

    //}
    //

    //
    //function lineNavigate(key, back)
    //{
    //    var lineData = getLineData(key);
    //    if (lineData) {
    //        if (!back) {
    //            window.history.pushState({}, lineData.name, '/' + key);
    //        }
    //        loadLine(lineData);
    //    }
    //}
    //
    //function loadLine(data)
    //{
    //    var page = document.getElementById('js-line-page'),
    //        title = document.getElementById('js-line-title'),
    //        statusPanel = document.getElementById('js-line-status-panel'),
    //        updatedPanel = document.getElementById('js-line-updated'),
    //        panelContent = '<h2>No information</h2>',
    //        updatedContent = 'N/A';
    //
    //    title.dataset.linebox = data.urlKey;
    //    title.innerHTML = data.name;
    //
    //    if (data.latestStatus) {
    //        panelContent = '<h2 class="g-unit">' + data.latestStatus.title + '</h2>';
    //        if (
    //            data.latestStatus.isDisrupted &&
    //            data.latestStatus.descriptions
    //        ) {
    //            data.latestStatus.descriptions.forEach(function(description){
    //               panelContent += '<p class="g-unit">' + description + '</p>';
    //            });
    //        }
    //        updatedContent = data.latestStatus.updatedAtFormatted;
    //    }
    //    statusPanel.innerHTML = panelContent;
    //    updatedPanel.innerHTML = updatedContent;
    //}


    // Cut the mustard
    if (document.addEventListener &&
        window.localStorage &&
        window.history
    ) {
        new TUBE();
    }
})();
