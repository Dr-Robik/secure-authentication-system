<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>

    <h2>Verify Your Email</h2>

    <p>Click the button below to verify your account:</p>

    <a href="{{ $link }}"
       style="display:inline-block;padding:10px 15px;background:#1a73e8;color:white;text-decoration:none;border-radius:5px;">
        Verify Email
    </a>

    <p>If the button does not work, copy this link:</p>

    <p>{{ $link }}</p>

</body>
</html>