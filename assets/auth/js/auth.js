(function (global) {
    const cAuth = {
        _cfg: {
            firebaseConfig: null,
            mapUser: (u) => ({
                uid: u.uid,
                email: u.email || "",
                displayName: u.displayName || "",
                photoURL: u.photoURL || "",
                phoneNumber: u.phoneNumber || "",
                providerId: (u.providerData?.[0]?.providerId) || "",
            }),
            onLoggedIn: null,
            onLoggedOut: null,
            onError: null,
        },

        async init(opts = {}) {
            this._cfg = { ...this._cfg, ...opts };
            if (!firebase.apps || !firebase.apps.length) {
                firebase.initializeApp(this._cfg.firebaseConfig);
            }
            this.auth = firebase.auth();
            try {
                await this.auth.setPersistence(firebase.auth.Auth.Persistence.LOCAL);
            } catch (err) {
                console.warn("setPersistence failed:", err);
            }

            this.auth.onAuthStateChanged(async (user) => {
                if(localStorage.getItem("cAuth.remove") == 1){
                    await this.logout();
                }else{
                    if(user){
                        vncoder.showLoading('Đang xử lý...');
                        let mapped;
                        try {
                            mapped = this._cfg.mapUser(user);
                        } catch (e) {
                            mapped = this._cfg.mapUser({ uid: user.uid });
                        }
                        try {
                            localStorage.setItem("cAuth.user", JSON.stringify(mapped));
                        } catch {}

                        try {
                            const res = await this.exchangeIdToken();
                            if (res && res.status === 1) {
                                window.location.assign(BASE_URL);
                                return;
                            }
                            if (typeof this._cfg.onLoggedIn === "function") {
                                this._cfg.onLoggedIn(mapped, user);
                            }
                            window.location.assign(BASE_URL);
                        } catch (e) {
                            this._emitError(e);
                        }
                        vncoder.hideLoading();
                    }
                }
            });
        },

        async logout(){
            try {
                await this.auth.signOut();
                localStorage.removeItem("cAuth.user");
                localStorage.removeItem("cAuth.remove");
            } catch {}
            if (typeof this._cfg.onLoggedOut === "function") {
                this._cfg.onLoggedOut();
            }
        },

        async exchangeIdToken() {
            const user = this.auth.currentUser;
            if (!user) throw new Error("Chưa đăng nhập");
            const idToken = await user.getIdToken(true);
            const res = await fetch(BASE_URL + "auth/session", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF_TOKEN },
                body: JSON.stringify({ idToken }),
                credentials: "same-origin",
			});
			if (!res.ok) {
                const text = await res.text().catch(() => "");
                throw new Error(`HTTP ${res.status}: ${text || "Request failed"}`);
            }
            try {
                return await res.json();
            } catch {
                return {};
            }
        },

        async login(providerName) {
            let provider;
            await this.logout();
            switch (providerName) {
                case "google":   provider = new firebase.auth.GoogleAuthProvider(); break;
                case "facebook": provider = new firebase.auth.FacebookAuthProvider(); break;
                case "twitter":  provider = new firebase.auth.TwitterAuthProvider(); break;
                case "github":   provider = new firebase.auth.GithubAuthProvider(); break;
                case "microsoft":provider = new firebase.auth.OAuthProvider('microsoft.com'); break;
                case "apple":    provider = new firebase.auth.OAuthProvider('apple.com'); break;
                default: throw new Error("Unsupported provider: " + providerName);
            }
            vncoder.showLoading();
            try {
                await this.auth.signInWithPopup(provider);
            } catch (e) {
                vncoder.hideLoading();
                this._emitError(e);
            }
        },

        getUser() {
            try { return JSON.parse(localStorage.getItem("cAuth.user") || "null"); }
            catch { return null; }
        },

        _emitError(e) {
            if (typeof this._cfg.onError === "function") this._cfg.onError(e);
        }
    };

    global.cAuth = cAuth;
})(window);

document.querySelector('#formData').addEventListener('submit', function () {
    vncoder.showLoading();
});
cAuth.init({firebaseConfig: firebaseConfig });