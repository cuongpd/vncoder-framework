<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success text-success">
            <code class="f-12 p-2">Đây là đoạn mã HTMl sẽ chèn vào thẻ head và trước khi đóng thẻ body của trang web</code>
        </div>
    </div>
    <div class="col-sm-12">
        {!! formBuilder($settingForm) !!}
    </div>
</div>


@push('footer')
    <script>
        const header = CodeMirror.fromTextArea(document.getElementById("header"), {
            lineNumbers: true, autofocus: true,lineWrapping: true, theme: "pastel-on-dark"
        });
        header.setSize("100%", 320);
        const footer = CodeMirror.fromTextArea(document.getElementById("footer"), {
            lineNumbers: true, autofocus: true,lineWrapping: true, theme: "pastel-on-dark"
        });
        footer.setSize("100%", 320);
    </script>

@endpush
