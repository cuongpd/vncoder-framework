var vncoder = {
    load: {
        css: function(e) {
            var t = document.getElementsByTagName("head")[0], a = document.createElement("link");
            a.rel = "stylesheet", a.type = "text/css", a.href = e, a.media = "all", t.appendChild(a);
        },
        js: function(e, t, a) {
            var n = document.createElement("script");
            if (n.type = "text/javascript", n.defer = !0, "function" == typeof t && (n.onreadystatechange = n.onload = function() {t()}), n.src = e, void 0 !== a && n.setAttribute("onerror", a), n) {for (var r = document.getElementsByTagName("script"), o = !1, c = 0; c < r.length; c++) r[c].src == e && (o = !0);o || document.getElementsByTagName("head")[0].appendChild(n)}
        }
    },
    is: {
        array: function(e) {
            return null != e && e.constructor == Array
        },
        bool: function(e) {
            return null != e && e.constructor == Boolean
        },
        string: function(e) {
            return e && /string/.test(typeof e)
        },
        func: function(e) {
            return null != e && e.constructor == Function
        },
        number: function(e) {
            return null != e && e.constructor == Number
        },
        object: function(e) {
            return null != e && e instanceof Object
        },
        element: function(e) {
            return null != e && e instanceof Element
        },
        blank: function(e) {
            return vncoder.util.trim(e) == ""
        },
        phone: function(e) {
            return /^(01([0-9]{2})|09[0-9])(\d{7})$/i.test(e)
        },
        exists: function(e) {
            return null != e && "undefined" != e && "undefined" != typeof e
        },
        email: function(e) {
            return /[a-z-_0-9\.]+@[a-z-_=>0-9\.]+\.[a-z]{2,3}$/i.test(e)
        },
        url: function(e) {
            return /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/.test(vncoder.util.trim(e))
        },
        image: function(e) {
            var t = e.substring(e.lastIndexOf("."), e.length).toLowerCase();
            return ".gif" == t || ".jpg" == t || ".png" == t || ".jpeg" == t
        }
    },
    util: {
        trim: function(e) {
            return e.replace(/^\s+|\s+$/g, "")
        },
        uuid: function() {
            return (new Date).getTime() + Math.random().toString().substring(2)
        },
        random: function(e, t) {
            return Math.floor(Math.random() * (t - e + 1)) + e
        },
        select: function(e) {
            input.focus();
            input.select();
        },
        enter: function(e, t) {
            if (done) {
                jQuery(e).keydown(function(event) {
                    if (event.keyCode == 13) {
                        done();
                    }
                });
            }
        },
        redirect: function(path) {
            window.location = path;
        },
        reload: function() {
            window.location.reload();
        },
        dump: function(data) {
            console.log(data);
        },
        stripHtml: function(n) {
            var t = document.createElement("DIV");
            return t.innerHTML = n, t.textContent || t.innerText || ""
        },
        splitAndWrap: function(element, delimiter, classNamePrefix, appendChar) {
            var contentArray = element.text().split(delimiter); // Chia theo delimiter (khoảng trắng hoặc ký tự)
            var resultHtml = "";

            $(contentArray).each(function(index, part) {
                var spanContent = "";

                // Nếu chia theo từ, tiếp tục chia từng từ thành ký tự
                if (delimiter === " ") {
                    spanContent = part
                        .split("") // Chia thành ký tự
                        .map(function(char, charIndex) {
                            return '<span class="char' + (charIndex + 1) + '">' + char + "</span>";
                        })
                        .join("");
                } else {
                    spanContent = part; // Nếu không chia thêm, chỉ giữ nguyên
                }

                resultHtml +=
                    '<span class="' +
                    classNamePrefix +
                    (index + 1) +
                    '" style="display: inline-block; transform: translate3d(0px, 0px, 0px);">' +
                    spanContent +
                    "</span>" +
                    appendChar;
            });

            element.html(resultHtml);
        }
    },
    get:{
        element: function(e) {
            return document.getElementById(e);
        },
        meta: function(e) {
            var t = document.getElementsByTagName("meta");
            for (var a in t) if (t[a].getAttribute("name") === e) return t[a].getAttribute("content");
            return ""
        }
    },
    cookie:{
        set: function(name, value, time = 30 * 86400, path = '/') {
            const date = new Date();
            date.setTime(date.getTime() + (time * 1000));
            const expires = "; expires=" + date.toUTCString();
            document.cookie = name + "=" + (value || "") + expires + "; path=" + path;
        },
        get: function(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        del: function(name, path = '/') {
            vncoder.cookie.set(name, "", -1, path);
        }
    },
    join: function(str) {
        var store = [str];
        return function extend(other) {
            if (other != null && 'string' == typeof other) {
                store.push(other);
                return extend;
            }
            return store.join('');
        }
    },
    copyText: function(codeId) {
        var codeElement = document.getElementById(codeId);
        var codeContent = codeElement.textContent;
        var tempTextArea = document.createElement('textarea');
        tempTextArea.value = codeContent;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextArea);
        vncoder.floatText("Đã copy : " + codeContent,1);
    },
    floatText : function(message, timeout = 1){
        if (!vncoder.is.string(message)) return;
        jQuery(".float-text").remove();
        jQuery("body").append('<div class="float-text">' + message + "</div>");
        jQuery(".float-loading").fadeTo("fast", 0.85);
        setTimeout(() => {
            jQuery(".float-text").fadeTo("slow", 0, function() {
                jQuery(this).remove();
            });
        }, timeout * 1000);
    }
};

vncoder.showLoading = function(message) {
    if(!vncoder.is.string(message)) message = "\u0110ang t\u1ea3i d\u1eef li\u1ec7u...";
    jQuery(".float-loading").remove();
    jQuery("body").append('<div class="float-loading">' + message + "</div>");
    jQuery(".float-loading").fadeTo("fast", 0.85);
};

vncoder.hideLoading = function() {
    jQuery(".float-loading").fadeTo("slow", 0, function() {
        jQuery(this).remove();
    });
};

vncoder.showMessage = function(message, status = 1, time = 3.5) {
    if (!vncoder.is.string(message)) return;
    let type = status > 0 ? 'success' : 'error';
    let toastContainer = document.getElementById('vn-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'vn-toast-container';
        document.body.appendChild(toastContainer);
    }
    const toast = document.createElement('div');
    toast.classList.add('vn-toast', type);
    const closeButton = document.createElement('button');
    closeButton.classList.add('vn-toast-close-btn');
    closeButton.innerHTML = '&times;';
    closeButton.onclick = function () {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.addEventListener('transitionend', () => {
            toast.remove();
            if (!toastContainer.hasChildNodes()) {
                toastContainer.remove();
            }
        });
    };

    const progressBar = document.createElement('div');
    progressBar.classList.add('vn-toast-progress');
    progressBar.style.animationDuration = `${time}s`;

    toast.textContent = message;
    toast.appendChild(closeButton);
    toast.appendChild(progressBar);
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.addEventListener('transitionend', () => {
            toast.remove();
            if (!toastContainer.hasChildNodes()) {
                toastContainer.remove();
            }
        });
    }, time * 1000);
}

vncoder.ajax = function(toUri, ajaxData, callback, method = 'GET') {
    jQuery.ajax({
        beforeSend: vncoder.showLoading(),
        url: BASE_URL + toUri,
        type: method,
        data: ajaxData,
        dataType: "json",
        success: function(data) {
            vncoder.hideLoading();
            if (data && vncoder.is.exists(data.status)) {
                vncoder.showMessage(data.message, data.status);
            }
            if (vncoder.is.exists(data.script)) {
                eval(data.script);
            }
            if (vncoder.is.exists(data.data) && vncoder.is.func(callback)) {
                callback(data.data);
            }
        },
        error: function(textStatus) {
            vncoder.hideLoading();
            vncoder.showMessage(textStatus, 'error');
        }
    });
};

vncoder.acceptCookie = function() {
    vncoder.cookie.set('accept_cookie', 1, 365 * 86400);
    jQuery('.cookie-notice').remove();
}

vncoder.animatedText = {
    init: function() {
        return this.each(function() {
            vncoder.util.splitAndWrap($(this), "", "char", "");
        });
    },
    words: function() {
        return this.each(function() {
            vncoder.util.splitAndWrap($(this), " ", "word", " ");
        });
    }
}

$.fn.animatedText = function(method) {
    if (vncoder.animatedText[method]) {
        return vncoder.animatedText[method].call(this);
    } else {
        return vncoder.animatedText.init.call(this);
        return this;
    }
};



