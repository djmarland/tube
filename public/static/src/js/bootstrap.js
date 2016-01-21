(function() {
    "use strict";
    // @todo - split this stuff into ES6 classes/modules
    var TUBE = function() {
        this.currentLine = null;
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
            mainPage : '[data-js="main-body"]',
            lineBox : '[data-js="linebox"]',
            lineBoxAlert : '[data-js="linebox-alert"]',
            lineBoxSummary : '[data-js="linebox-summary"]',
            notificationsPanel : '[data-js="notifications-panel"]',
            notificationsSave : '[data-js="notifications-save"]',
            notificationsDelete : '[data-js="notifications-delete"]',
            notificationsStatus : '[data-js="notifications-progress"]',
            notificationsSettings : '[data-js="notifications-settings"]'
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
        sessionData : {},
        updateInterval : (1000 * 60 * 2), // 2 minutes
        database : null,
        safeResponse : function(request) {
            return (request.status >= 200 && request.status < 400);
        },
        ajax : function(url, callback, errorCallback) {
            var request = new XMLHttpRequest();
            request.open('GET', url, true);

            request.onload = function() {
                if (this.safeResponse(request)) {
                    return callback(request.response);
                }
                if (errorCallback) {
                    return errorCallback(request.response);
                }
            }.bind(this);

            request.onerror = function() {
                // There was a connection error of some sort
                if (errorCallback) {
                    return errorCallback(request.response);
                }
            };
            request.send();
        },
        getDatabaseTable : function() {
            var transaction = this.database ? this.database.transaction(['data'], 'readwrite') : null;
            return transaction ? transaction.objectStore('data') : null;
        },
        setData : function(key, value, callback) {
            var tbl, request;
            this.sessionData[key] = value;
            tbl = this.getDatabaseTable();
            if (tbl) {
                request = tbl.put(value, key);
                request.onsuccess = function () {
                    if (callback) {
                        callback();
                    }
                };
            } else if (callback) {
                callback();
            }
        },
        getData : function(key, callback) {
            var tbl;
            if (this.sessionData[key]) {
                if (callback) {
                    return callback(this.sessionData[key]);
                }
                return this.sessionData[key];
            }
            tbl = this.getDatabaseTable();
            if (tbl) {
                tbl.get(key).onsuccess = function (event) {
                    var result = event.target.result;
                    this.sessionData[key] = result;
                    if (callback) {
                        callback(result);
                    }
                }.bind(this);
            }
        },
        initWithDB : function() {
            var DB = indexedDB.open('TubeLines', 1);
            DB.onsuccess = function(evt){
                this.database = evt.target.result;
                this.getData(this.storage.allLines, function(data) {
                    var d1,d2;
                    if (data && window.TubeAlert.linedata) {
                        data = JSON.parse(data);
                        d1 = new Date(data.lines[0].latestStatus.updatedAt);
                        d2 = new Date(window.TubeAlert.linedata[0].latestStatus.updatedAt);
                        if (d2 > d1) {
                            this.setData(this.storage.allLines, JSON.stringify({lines: window.TubeAlert.linedata}));
                        }
                    } else if (window.TubeAlert.linedata) {
                        this.setData(this.storage.allLines, JSON.stringify({lines:window.TubeAlert.linedata}));
                    }
                    this.updateState();
                }.bind(this));
                this.refreshData();
                setInterval(this.refreshData.bind(this), this.updateInterval);
                if ('serviceWorker' in navigator &&
                    'content' in document.createElement('template')
                ) {
                    navigator.serviceWorker.register('/service-worker.js', {scope:'/'})
                        .then(this.initialiseServiceWorker.bind(this));
                }
            }.bind(this);
            DB.onupgradeneeded = function (evt) {
                var dbobject = evt.target.result;
                if (evt.oldVersion < 1) {
                    // Create our object store and define indexes.
                    dbobject.createObjectStore('data');
                }
            }.bind(this);
        },
        init : function() {
            this.getTemplates();
            this.addListeners();
            if (window.indexedDB) {
                return this.initWithDB();
            }
            if (window.TubeAlert.linedata) {
                this.setData(this.storage.allLines, JSON.stringify({lines:window.TubeAlert.linedata}));
            }
            this.refreshData();
            setInterval(this.refreshData.bind(this), this.updateInterval);
        },

        updateState : function() {
            // using incoming data
            // if is newer than what is already on the page, replace it
            var lineboxes = document.querySelectorAll(this.selectors.lineBox),
                linebox,
                linedata,
                l = lineboxes.length,
                d1, d2, i;
            for (i=0;i<l;i++) {
                linebox = lineboxes[i];
                linedata = this.getLineData(linebox.dataset.linebox);
                if (!linedata) {
                    continue;
                }
                d1 = new Date(linebox.dataset.updated);
                d2 = new Date(linedata.latestStatus.updatedAt);
                if (d2 > d1) {
                    linebox.dataset.updated = linedata.latestStatus.updatedAt;
                    linebox.querySelector(this.selectors.lineBoxSummary).textContent = linedata.statusSummary;
                    if (linedata.isDisrupted) {
                        linebox.querySelector(this.selectors.lineBoxAlert).classList.remove('hidden');
                    } else {
                        linebox.querySelector(this.selectors.lineBoxAlert).classList.add('hidden');
                    }

                    // if currently looking at this line page, it needs updating
                    if (this.currentLine == linedata.urlKey) {
                        this.swapBody(this.transformLineTemplate(this.lineTemplate, linedata));
                        this.setNotificationsPanel(linedata.urlKey);
                    }
                }
            }
        },
        initialiseServiceWorker : function() {
            var body = document.body;
            if (this.currentLine) {
                this.setNotificationsPanel(this.currentLine);
            } else if (body.dataset.view == this.bodyView.settings) {
                this.setNotificationsSettings();
            }
        },
        stringToDom : function(string) {
            var doc = document.createElement('div');
            doc.innerHTML = string;
            return doc;
        },
        getTemplates : function() {
            var body = document.body,
                currentPage = document.getElementById(this.mainPageID).cloneNode(true);

            this.currentLine = null;

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
                this.currentLine = body.dataset.pageline;
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
                this.setData(this.storage.allLines, response, this.updateState.bind(this));
            }.bind(this));
        },
        swapBody : function(data) {
            window.scrollTo(0, 0);
            this.mainArea.innerHTML = data.innerHTML;
        },
        goHome : function(popped) {
            var path = '/',
                body = document.body;
            if (!this.homeTemplate) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.homeTemplate);
            document.body.dataset.view = this.bodyView.home;
            body.dataset.pageline = null;
            this.currentLine = null;
            if (!popped) {
                document.title = 'TubeAlert';
                window.history.pushState({}, 'TubeAlert', path);
            }
        },
        goToSettings : function(popped) {
            var path = '/settings',
                body = document.body;
            if (!this.settingsTemplate) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.settingsTemplate);
            this.setNotificationsSettings();
            body.dataset.view = this.bodyView.settings;
            body.dataset.pageline = null;
            this.currentLine = null;
            if (!popped) {
                document.title = 'Settings - TubeAlert';
                window.history.pushState({}, 'Settings - TubeAlert', path);
            }
        },
        goToLine : function(lineKey, popped) {
            var path = '/' + lineKey,
                lineData = this.getLineData(lineKey),
                body = document.body;
            if (!this.lineTemplate || !lineData) {
                window.location.href = path;
                return;
            }
            this.swapBody(this.transformLineTemplate(this.lineTemplate, lineData));
            this.setNotificationsPanel(lineKey);
            body.dataset.view = this.bodyView.line;
            body.dataset.pageline = lineKey;
            this.currentLine = lineKey;
            if (!popped) {
                document.title = lineData.name + ' - TubeAlert';
                window.history.pushState({}, lineData.name + ' - TubeAlert', path); // @todo - sort titles
            }
        },
        setNotificationsPanel : function(lineKey) {
            var target = document.querySelector(this.selectors.notificationsPanel),
                button = document.querySelector(this.selectors.notificationsSave),
                notificationsTemplate = document.getElementById('template-notify'),
                panel = document.importNode(notificationsTemplate.content, true);

            if (!('ServiceWorkerRegistration' in window) ||
                !('showNotification' in ServiceWorkerRegistration.prototype) ||
                !('PushManager' in window)) {
                return; //push notifications not supported
            }

            if (Notification.permission === 'denied') {
                target.innerHTML = 'You have denied access for notifications';
                return;
            }

            button.disabled = false;

            button.addEventListener('click', function() {
                button.disabled = true;
                this.updateSubscription(lineKey, panel);
            }.bind(this));

            this.getData('times', function(data) {
                if (data) {
                    data = JSON.parse(data);
                    if (data[lineKey]) {
                        panel = this.setTimes(JSON.parse(data[lineKey]), panel);
                    }
                }
                target.innerHTML = '';
                target.appendChild(panel);
            }.bind(this));
        },
        setNotificationsSettings : function() {
            var target = document.querySelector(this.selectors.notificationsSettings),
                settingsTemplate = document.getElementById('template-settings'),
                panel = document.importNode(settingsTemplate.content, true),
                button;

            if (!('ServiceWorkerRegistration' in window) ||
                !('showNotification' in ServiceWorkerRegistration.prototype) ||
                !('PushManager' in window)) {
                return; //push notifications not supported
            }

            if (Notification.permission === 'denied') {
                target.innerHTML = 'You have denied access for notifications';
                return;
            }

            target.innerHTML = '';
            target.appendChild(panel);

            button = document.querySelector(this.selectors.notificationsDelete);

            button.addEventListener('click', function() {
                button.disabled = true;
                this.cancelSubscription(panel, button);
            }.bind(this));
        },
        setTimes : function(times, panel) {
            var l = times.length,
                i, c;
            for (i=0;i<l;i++) {
                c = panel.querySelector('input[value="' + times[i] + '"]');
                if (c) {
                    c.checked = true;
                }
            }
            return panel;
        },
        updateStatus : function(statusMsg) {
            var status = document.querySelector(this.selectors.notificationsStatus);
            status.innerHTML = statusMsg;
        },
        updateSubscription : function(lineKey, panel) {
            var checkboxes = panel.querySelectorAll('[type="checkbox"]'),
                button = document.querySelector(this.selectors.notificationsSave),
                l = checkboxes.length,
                times = JSON.stringify(this.getTimes());

            this.updateStatus('<span class="loading loading--leading"></span>Saving...');

            this.getData('times', function(data) {
                if (data) {
                    data = JSON.parse(data);
                } else {
                    data = {}
                }
                data[lineKey] = times;
                this.setData('times', JSON.stringify(data));
            }.bind(this));

            navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
                serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly:true})
                    .then(function(subscription) {
                        var url = '/notifications/subscribe?endpoint=' + subscription.endpoint + '&line=' + this.currentLine;

                        // The subscription was successful
                        button.disabled = false;

                        url += '&times=' + times;

                        this.ajax(url, function(data) {
                            var status = JSON.parse(data);
                            if (status.status && status.status === 'ok') {
                                this.updateStatus('Saved');
                                return;
                            }
                            this.updateStatus('An error occurred');
                        }.bind(this), function() {
                            this.updateStatus('An error occurred');
                        }.bind(this));
                    }.bind(this))
                    .catch(function(e) {
                        if (Notification.permission === 'denied') {
                            this.updateStatus('Permission denied');
                            button.disabled = false;
                        } else {
                            this.updateStatus('Unable to subscribe');
                            button.disabled = false;
                        }
                    }.bind(this));
            }.bind(this));
        },
        cancelSubscription : function(panel, button) {
            this.updateStatus('<span class="loading loading--leading"></span>Saving...');

            navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
                serviceWorkerRegistration.pushManager.getSubscription().then(
                    function(pushSubscription) {
                        var url = '/notifications/unsubscribe?endpoint=' + pushSubscription.endpoint;
                        // Check we have everything we need to unsubscribe
                        if (!pushSubscription || !pushSubscription.unsubscribe) {
                            return;
                        }

                        pushSubscription.unsubscribe();
                        // clear from indexedDB
                        this.setData('times', JSON.stringify({}));
                        // send a message saying cleared
                        this.ajax(url, function(data) {
                            var status = JSON.parse(data);
                            if (status.status && status.status === 'ok') {
                                this.updateStatus('Unsubscribed');
                                return;
                            }
                            this.updateStatus('An error occurred');
                        }.bind(this), function() {
                            this.updateStatus('An error occurred');
                        }.bind(this));
                        button.disabled = false;
                    }.bind(this)).catch(function(e) {
                        this.updateStatus('No subscription found. You\'re clear.');
                }.bind(this));
            }.bind(this));

            setTimeout(function() {
                button.disabled = false;
                this.updateStatus('Done');
            }.bind(this), 2000);
        },
        getTimes : function() {
            var panel = document.querySelector(this.selectors.notificationsPanel),
                checkboxes = panel.querySelectorAll('[type="checkbox"]'),
                l = checkboxes.length,
                i, c,
                times = [];

            for (i=0;i<l;i++) {
                c = checkboxes[i];
                if (c.checked) {
                    times.push(c.value);
                }
            }
            return times;
        },
        transformLineTemplate : function(template, data) {
            var page = this.stringToDom(template.innerHTML),
                title = page.querySelector('[data-js="line-title"]'),
                statusPanel = page.querySelector('[data-js="line-status-panel"]'),
                updatedPanel = page.querySelector('[data-js="line-updated"]'),
                panelContent = '<h2>No information</h2>',
                updatedContent = 'N/A';

            page.querySelector('[data-js="notifyHead"]').dataset.linebox = data.urlKey;
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
            var saved = this.getData(this.storage.allLines),
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
        navigate : function(path, popped) {
            // a very simple router
            if (path == '/') {
                return this.goHome(popped);
            }
            if (path == '/settings') {
                return this.goToSettings(popped);
            }
            path = path.replace('/','');
            return this.goToLine(path, popped);
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
                this.navigate(document.location.pathname, true);
            }.bind(this);
        }
    };

    // Cut the mustard
    if (document.querySelector &&
        document.addEventListener &&
        window.history
    ) {
        new TUBE();
    }
})();
