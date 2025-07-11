<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger">
            <code class="f-12">Trong blade, sử dụng <strong><i>@{{ getConfig('key', 'default-value', 'description') }}</i></strong> để tùy chỉnh dữ liệu `key` cần thay đổi, `default-value` là giá trị mặc định khởi tạo ( bool, int, string)  `description` là mô tả chỉ hiện trên trang quản trị.</code>
        </div>
    </div>
    <div class="col-sm-12">
        {!! formHorizontalBuilder($settingForm) !!}
    </div>
</div>
