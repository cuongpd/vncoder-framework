<form action="{{ $__currentBackendUrl }}" method="POST" autocomplete="off" id="formData">
    <div class="row mb-3">
        <label for="welcome_text" class="col-2 col-form-label">Welcome Text</label>
        <div class="col-10">
            <input id="welcome_text" name="welcome_text" type="text" required class="form-control" value="{{ $settingForm['welcome_text'] }}">
            <div id="welcome_textHelpBlock" class="form-text text-muted">Lời chào bên phải</div>
        </div>
    </div>

    <div class="row mb-3">
        <label for="info_text" class="col-2 col-form-label">Info Text</label>
        <div class="col-10">
            <textarea id="info_text" name="info_text" rows="5" class="form-control">{{$settingForm['info_text']}}</textarea>
            <div id="info_textHelpBlock" class="form-text text-muted">Giới thiệu thông tin website</div>
        </div>
    </div>

    <div class="row mb-3">
        <label for="firebase_version" class="col-2 col-form-label">Firebase Version</label>
        <div class="col-10">
            <input id="firebase_version" name="firebase_version" placeholder="Phiên bản Firebase" type="text" required class="form-control" value="{{ $settingForm['firebase_version'] }}">
        </div>
    </div>

    <div class="row mb-3">
        <label for="firebase_service_account_key" class="col-2 col-form-label">Firebase Service Account</label>
        <div class="col-10">
            <textarea id="firebase_service_account_key" name="firebase_service_account_key" rows="10" class="form-control">{{ $settingForm['firebase_service_account_key'] }}</textarea>
            <div id="firebase_service_account_keyHelpBlock" class="form-text">
                Truy cập Project settings → Service accounts → Firebase Admin SDK → Generate new private key, chép toàn bộ nội dung file .json vào đây
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <label for="firebase_config" class="col-2 col-form-label">Firebase Config</label>
        <div class="col-10">
            <textarea id="firebase_config" name="firebase_config" rows="3" class="form-control">{{ $settingForm['firebase_config'] }}</textarea>
            <div id="firebase_configHelpBlock" class="form-text">
                Truy cập Project settings → General → Chọn hoặc tạo Web apps → SDK setup and configuration, chọn Config và lấy nội dung `const firebaseConfig = ...`
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-2 col-form-label">Sign-in Providers</label>
        <div class="col-10">
            @foreach($authProviders as $provider)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="firebase_sign_in_providers_{{$provider}}" name="firebase_sign_in_providers[]" value="{{$provider}}" @if(in_array($provider, $settingForm['firebase_sign_in_providers'])) checked @endif>
                    <label class="form-check-label" for="firebase_sign_in_providers_{{$provider}}">{{ucfirst($provider)}}</label>
                </div>
            @endforeach
            <div id="firebase_sign_in_providersHelpBlock" class="form-text">
                Vào Authentication → Sign-in method → Đăng kí các Sign-in providers hỗ trợ, chọn các Sign-in providers đã đăng ký
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="offset-2 col-10">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>