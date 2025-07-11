<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>API Documents</title>
    <link rel="stylesheet" type="text/css" href="{{core_static('api/ui.css')}}" >
    <link rel="stylesheet" type="text/css" href="{{core_static('api/dark.css')}}?v=1" >
</head>

<body>
<div id="swagger-ui"></div>
<script src="{{core_static('api/ui-bundle.js')}}"></script>
<script src="{{core_static('api/ui-preset.js')}}"></script>

<script>
    window.onload = function() {
        const ui = SwaggerUIBundle({
            url: "{{url('api/open-api.json')}}",
            dom_id: "#swagger-ui",
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ]
        });
        window.ui = ui;
    }
</script>

</body>
</html>
