(function () {
    /* Vars */
    var rapanel = document.getElementById('ra-panel');
    var loading = document.createElement('div');
    var spinner = document.createElement('svg');
    var labelPanel = document.getElementById('ra-panel-label');
    var menu = document.getElementById('ra-panel-actions');
    var timerId;
    var menuPosition;
    var editMenu = document.getElementById('ra-panel-edit-menu');
    var lock = document.getElementById('ra-panel-lock');
    var icoMenu = document.getElementById('ra-panel-menu');
    var moveEnable = 0;

    /* Lock/unlock menu */
    lock.addEventListener('click', function (event) {
        event.preventDefault();
        rapanel.classList.toggle('ra-panel-fixed');
        this.classList.toggle('ra-unlock');
        labelPanel.classList.toggle('ra-panel-label-fixed');
        if (rapanel.classList.contains('ra-panel-fixed')) {
            sessionStorage.setItem('fixed', 'fixed');
        } else {
            sessionStorage.setItem('fixed', 'unfixed');
        }
    });

    /* Clear cache */
    document.getElementById('ra-panel-reset').addEventListener('click', function (event) {
        event.preventDefault();
        $.get(event.target.href).done(function () {
            location.reload();
        });
        event.target.classList.add('ra-loading');
        addLoading();
    });

    /* On/off debug mode */
    document.getElementById('ra-panel-debug').addEventListener('click', function (event) {
        event.preventDefault();
        debugMode(this);
    });

    var menuItems = document.querySelectorAll('#ra-panel-actions a');

    for (var i = 0; i < menuItems.length; i++) {
        menuItems[i].addEventListener("mouseover", function (event) {
            if (!isInside(event.relatedTarget, this)) {
                addTooltip(this);
                if (menu.classList.contains('ra-show-sub-menu') && event.target.id == 'ra-panel-menu') {
                    this.classList.remove('hasTooltip');
                    removeTooltip();
                }
            }
        });
    }

    for (var j = 0; j < menuItems.length; j++) {
        menuItems[j].addEventListener("mouseout", function (event) {
            if (!isInside(event.relatedTarget, this)) {
                this.classList.remove('hasTooltip');
                removeTooltip();
            }
        });
    }
    if (getCookie('debug') == '1')
        document.getElementById('ra-panel-debug').classList.toggle('ra-debug-active');

    /* Show menu */
    document.getElementById('ra-panel-label').addEventListener('click', function () {
        if (moveEnable == 1) {
            console.log('1');
            return;
        }
        rapanel.classList.remove('ra-panel-offset');
    });

    rapanel.addEventListener('mouseout', function (event) {
        if (!document.querySelector('.ra-panel-loading')) {
            if (!isInside(event.relatedTarget, this)) {
                timerId = setTimeout(function () {
                    if (document.getElementById('ra-panel-lock').getAttribute('class') !== 'ra-unlock') {
                        rapanel.classList.add('ra-panel-offset');
                        document.getElementById('ra-panel-label').classList.add('ra-panel-label-fixed');
                    }
                    menu.classList.remove('ra-show-sub-menu');
                }, 500);
            }
        }
    });

    rapanel.addEventListener('mouseover', function () {
        clearTimeout(timerId);
    });

    window.addEventListener('mouseup', function () {
        clearTimeout(timerId);
    });

    /* Drag panel */
    labelPanel.addEventListener('mousedown', function (event) {
        var target = event.target;
        var el = this;
        timerId = setTimeout(function () {
            moveEnable = 1;
            el.classList.add('ra-panel-label-drag');
            var move = function (event) {
                if (target) {
                    menuPosition = el.style.left = event.clientX + 'px';
                    if (parseInt(menuPosition) < el.clientWidth / 2) {
                        el.style.left = el.clientWidth / 2 + 'px';
                    }
                    if (parseInt(menuPosition) > document.body.clientWidth - el.clientWidth / 2) {
                        el.style.left = document.body.clientWidth - el.clientWidth / 2 + 'px';
                    }
                    // document.body.style.cursor = 'move';
                    // labelPanel.style.cursor = 'move';
                }
            };
            window.addEventListener('mousemove', move);
            window.addEventListener('mouseup', function () {
                if (target.classList.contains('ra-panel-label-drag')) {
                    target.classList.remove('ra-panel-label-drag');
                }
                setTimeout(function () {
                    moveEnable = 0;
                }, 1);
                // document.body.style.cursor = 'default';
                // labelPanel.style.cursor = 'pointer';
                setCookie('position', menuPosition);
                offsetMenu();
                this.removeEventListener('mousemove', move);
            });
        }, 300);
    });

    if (parseInt(getCookie('position')) > document.body.clientWidth) {
        labelPanel.style.left = document.body.clientWidth / 2 + 'px';
        setCookie('position', labelPanel.style.left = document.body.clientWidth / 2 + 'px');
    } else {
        labelPanel.style.left = getCookie('position');
    }

    offsetMenu();

    if (sessionStorage.getItem('fixed') == 'unfixed') {
        lock.classList.add('ra-unlock');
        rapanel.classList.remove('ra-panel-fixed');
        rapanel.classList.remove('ra-panel-offset');
        labelPanel.classList.remove('ra-panel-label-fixed');
    }

    document.getElementById('ra-panel-edit').addEventListener('click', function (event) {
        event.preventDefault();
        editMode(event.target.href);
    });

    /*document.getElementById('ra-panel-edit').addEventListener('dblclick', function () {
     location.href = this.href;
     });*/

    document.querySelector('#ra-panel-edit-menu .removeEditMode').addEventListener('click', closeEditMode);

    icoMenu.addEventListener('click', function (e) {
        menu.classList.toggle('ra-show-sub-menu');
        if (menu.classList.contains('ra-show-sub-menu')) {
            this.classList.remove('hasTooltip');
            removeTooltip();
        }
        e.preventDefault();
    });

    /* Функция добавляет окно загрузчик поверх экарана */
    function addLoading() {
        var block = document.body.insertBefore(loading, document.body.childNodes[0]);
        loading.classList.add('ra-panel-loading');
        block.innerHTML = '<svg width="32px" height="32px"><use xlink:href="svg-symbols.svg#spinner"></use></svg>';
    }

    /* Функция удаляет окно загрузчик */
    function removeLoading() {
        loading.classList.remove('ra-panel-loading');
        loading.parentNode.removeChild(loading);
    }

    /* Функция переключения класса debug режима */
    function debugMode(el) {
        el.classList.toggle('ra-debug-active');
        if (el.classList.contains('ra-debug-active')) {
            setCookie('debug', 1);
        } else {
            setCookie('debug', '');
        }
        location.reload();
    }

    /* Функция вывода подсказок при наведении */
    function addTooltip(el) {
        var half = el.clientWidth / 2;
        var leftPos = el.getBoundingClientRect().left;
        var tooltip = document.createElement('div');
        var data = el.dataset;

        if (el.hasAttribute('data-ra-tooltip') && el.getAttribute('data-ra-tooltip') !== '') {
            document.body.insertBefore(tooltip, document.body.childNodes[0]);
            el.classList.add('hasTooltip');
            tooltip.classList.add('ra-tooltip');
        }

        tooltip.innerText = data.raTooltip;
        tooltip.style.top = rapanel.clientHeight + 'px';
        var stringLength = stringSize(tooltip, tooltip.innerText);
        tooltip.style.width = stringLength.width + 24 + 'px';

        var tooltipWidth = tooltip.clientWidth;
        tooltip.style.left = leftPos + half - (tooltipWidth / 2) + 'px';

        if (tooltip.getBoundingClientRect().left < 0) {
            tooltip.style.left = 0 + 'px';
        }
        if (tooltip.getBoundingClientRect().right > document.body.clientWidth) {
            tooltip.style.left = document.body.clientWidth - tooltipWidth + 'px';
        }
    }

    /* Удаление подсказки */
    function removeTooltip() {
        var tooltip = document.querySelector('.ra-tooltip');
        if (tooltip) {
            tooltip.parentNode.removeChild(tooltip);
        }
    }

    function isInside(node, target) {
        for (; node != null; node = node.parentNode)
            if (node == target) return true;
    }

    /* Функция считает длину текста */
    function stringSize(obj, str) {
        var s = document.createElement("span");
        s.innerHTML = str;
        s.style.visibility = "hidden";
        s.style.whiteSpace = "nowrap";
        obj.appendChild(s);
        var res = {width: s.offsetWidth, height: s.offsetHeight};
        obj.removeChild(s);
        return res;
    }

    /* Функция для передвижения панели */
    function movePanel(el) {

    }

    function editMode(url) {
        var iframe = document.createElement('iframe');
        document.body.insertBefore(iframe, document.body.childNodes[0]);
        iframe.classList.add('ra-panel-edit-mode');
        iframe.setAttribute('src', url);
        iframe.setAttribute('name', "editableFrame");
        editMenu.style.display = 'block';
        addLoading();

        iframe.onload = function () {
            removeLoading();
        };

        var escape = function (event) {
            if (event.keyCode == 27) {
                closeEditMode();
            }
        };

        if (document.getElementsByClassName('ra-panel-edit-mode')) {
            window.addEventListener('keydown', escape);
        }

        window.addEventListener('keyup', function () {
            this.removeEventListener('keydown', escape);
        });

        document.body.style.overflow = 'hidden';

    }

    function closeEditMode() {
        var iframe = document.querySelector('.ra-panel-edit-mode');
        iframe.parentNode.removeChild(iframe);
        editMenu.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function setCookie(name, value, options) {
        options = options || {};

        var defaultOptions = {
            path: '/'
        };

        for (var propName in defaultOptions) {
            if (!options[propName]) options[propName] = defaultOptions[propName];
        }

        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function offsetMenu() {
        var menuWidth = menu.clientWidth;
        var offset = menu.style.marginLeft = parseInt(getCookie('position')) - menuWidth / 2 + 'px';

        if (parseInt(offset) < 0) {
            menu.style.marginLeft = 0 + 'px';
        } else if (parseInt(offset) + menuWidth > document.body.clientWidth) {
            menu.style.marginLeft = document.body.clientWidth - menuWidth + 'px';
        }
    }
})();


