<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger text-success">
            <code class="f-12">Khi bật chế độ bảo trì, toàn bộ website sẽ chuyển hướng truy cập sang link {{url('maintenance.html')}}, việc truy cập vào trang quản trị vẫn hoạt động bình thường.</code>
        </div>
    </div>
    <div class="col-sm-12">
        {!! formBuilder($settingForm) !!}
    </div>
</div>