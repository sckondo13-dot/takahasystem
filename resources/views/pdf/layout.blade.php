<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">

    <style>
        @font-face {
            font-family: 'Noto Sans JP';
            src:url("{{ resource_path('fonts/NotoSansJP-Regular.ttf') }}") format("truetype");
        }

        @font-face {
            font-family: 'Noto Sans JP';
            font-weight: bold;
            src:url("{{ resource_path('fonts/NotoSansJP-Bold.ttf') }}") format("truetype");
        }

        * {
            box-sizing: border-box;
        }

        @page {
            margin: 12mm 10mm;
        }

        body {
            font-family: 'Noto Sans JP';
            font-size: 9px;
            color: #222;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 0.6px solid #555;
            background: #f2f2f2;
            padding: 3px;
            font-weight: bold;
        }

        td {
            border: 0.6px solid #555;
            padding: 3px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 8px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 15px;
        }

        .header {
            width: 100%;
            margin-bottom: 8px;
        }

        .company {
            float: left;
            font-size: 12px;
            font-weight: bold;
        }

        .created {
            float: right;
            font-size: 8px;
        }

        .clear {
            clear: both;
        }

        .info {
            margin-bottom: 10px;
        }

        .info td {
            border: none;
            padding: 2px 0;
        }

        .footer {
            margin-top: 10px;
        }

        .footer-left {
            width: 58%;
            float: left;
        }

        .footer-right {
            width: 38%;
            float: right;
        }

        .stamp-area {
            margin-top: 12px;
        }

        .sun {
            color: #d40000;
        }

        .sat {
            color: #0066cc;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

    @stack('style')

</head>

<body>

    @yield('content')

</body>

</html>