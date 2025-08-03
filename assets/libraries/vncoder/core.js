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
            var contentArray = element.text().split(delimiter);
            var resultHtml = "";

            $(contentArray).each(function(index, part) {
                var spanContent = "";
                if (delimiter === " ") {
                    spanContent = part.split("").map(function(char, charIndex) {
                        return '<span class="char' + (charIndex + 1) + '">' + char + "</span>";
                    }).join("");
                } else {
                    spanContent = part;
                }
                resultHtml += '<span class="' + classNamePrefix + (index + 1) + '" style="display: inline-block; transform: translate3d(0px, 0px, 0px);">' + spanContent + "</span>" + appendChar;
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
        set : function(name, value, time = 30 * 86400, path = '/') {
            const date = new Date();
            date.setTime(date.getTime() + (time * 1000));
            const expires = "; expires=" + date.toUTCString();
            document.cookie = name + "=" + (value || "") + expires + "; path=" + path;
        },
        get : function(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        remove : function(name, path = '/') {
            vncoder.cookie.set(name, "", -1, path);
        }
    },
    animatedText:{
        init : function() {
            return this.each(function() {
                vncoder.util.splitAndWrap($(this), "", "char", "");
            });
        },
        words : function() {
            return this.each(function() {
                vncoder.util.splitAndWrap($(this), " ", "word", " ");
            });
        }
    },
    acceptCookie : function() {
        vncoder.cookie.set('accept_cookie', 1, 365 * 86400);
        jQuery('.cookie-notice').remove();
    },
    join : function(str) {
        var store = [str];
        return function extend(other) {
            if (other != null && 'string' == typeof other) {
                store.push(other);
                return extend;
            }
            return store.join('');
        }
    },
    copyText : function(codeId) {
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
        jQuery(".float-text").fadeTo("fast", 0.85);
        setTimeout(() => {
            jQuery(".float-text").fadeTo("slow", 0, function() {
                jQuery(this).remove();
            });
        }, timeout * 1000);
    },
    showLoading : function(message) {
        if(!vncoder.is.string(message)) message = "\u0110ang t\u1ea3i d\u1eef li\u1ec7u...";
        jQuery(".float-loading").remove();
        jQuery("body").append('<div class="float-loading">' + message + "</div>");
        jQuery(".float-loading").fadeTo("fast", 0.85);
    },
    hideLoading : function() {
        jQuery(".float-loading").fadeTo("slow", 0, function() {
            jQuery(this).remove();
        });
    },
    safeText : function(str) {
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/gui, "a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/gui, "e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/gui, "i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/gui, "o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/gui, "u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/gui, "y");
        str = str.replace(/đ/g, "d");
        str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
        str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/gui, "E");
        str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
        str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/gui, "O");
        str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/gui, "U");
        str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/gui, "Y");
        str = str.replace(/Đ/gui, "D");
        str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, "");
        str = str.replace(/\u02C6|\u0306|\u031B/g, "");
        str = str.replace(/\(|\)/gui, "");
        str = str.replace(/\./gui, "-");
        str = str.replace(/ /gui, "-");
        str = str.replace(/--/gui, "-");
        return str.replace("--", "-").toLowerCase();
    }
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
        url: (toUri.startsWith('http://') || toUri.startsWith('https://')) ? toUri : (typeof BASE_URL !== "undefined" ? BASE_URL : "") + toUri,
        type: method,
        data: ajaxData,
        dataType: "json",
        success: function(data) {
            vncoder.hideLoading();
            if (vncoder.is.exists(data.data) && vncoder.is.func(callback)) {
                callback(data.data);
            }else{
                if (data && vncoder.is.exists(data.status) && vncoder.is.exists(data.message)) {
                    vncoder.showMessage(data.message, data.status);
                }
            }
        },
        error: function(textStatus) {
            vncoder.hideLoading();
            vncoder.showMessage(textStatus, 'error');
        }
    });
};


vncoder.showModal = function (title, content, options = {}) {
    let modal = document.createElement('div');
    modal.classList.add('vn-modal');
    modal.innerHTML = `
        <div class="vn-modal-content">
            <span class="vn-modal-close">&times;</span>
            <h2>${title}</h2>
            <div class="vn-modal-body">${content}</div>
            <div class="vn-modal-footer">
                ${options.buttons ? options.buttons.map(btn => `<button class="vn-modal-btn" onclick="${btn.action}">${btn.label}</button>`).join('') : ''}
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    let closeButton = modal.querySelector('.vn-modal-close');
    closeButton.onclick = function() {
        modal.remove();
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.remove();
        }
    };

    return modal;
};


$.fn.animatedText = function(method) {
    if (vncoder.animatedText[method]) {
        return vncoder.animatedText[method].call(this);
    } else {
        return vncoder.animatedText.init.call(this);
        return this;
    }
};



