<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>Test Arabic Font</title>
    <style>
        body {
            font-family: 'sultan';
            /* font-weight: bolder; */
            /* Use Sultan font */
            direction: rtl;
            /* Set text direction to right-to-left */
            text-align: right;
            /* Align text to the right */
        }

        .content {
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1>{{$letter->prefix}} {{__('/')}} {{$letter->sender}}</h1>
        <h1>{{$letter->suffix}}</h1>
    </div>
</body>

</html>
