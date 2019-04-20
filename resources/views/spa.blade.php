<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>etx-pos</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body, html {
            overflow-y: hidden !important;
        }
        .form-icon {
            width: 70px;
            height: 70px;
        }
        input.form-control, select.form-control {
            border-radius: 0 !important;
        }
        .btn, .card {
            border-radius: 0 !important;
        }
        .my-loader {
            width: 100%;
            height: 100%;
            position: absolute;
            background-color: #f2f3f4;
            z-index: 88888;
            top: 0;
            left: 0;
            display: flex;
            flex-direction:row;
            text-align: center;
            justify-content: center;
            align-items: center;
            background-image: url('/img/ball.svg');
            background-repeat: no-repeat;
            background-position: center;
        }
        .my-loader-content {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            background-color: red;
        }
    </style>
</head>
<body>
    <div id="app" class="main">
        <app></app>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="/js/bootstrap.min.js"></script>
</body>
</html>