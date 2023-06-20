
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Unity Pump | Payment</title>
    <style>
        .custom-iframe {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                width: 100%;
                height: 100%;
                border: none;
            }
    </style>
</head>
<body>
    <iframe
        src="{{ $xendit_invoice_url }}"
        class="custom-iframe"></iframe>

</body>
</html>
