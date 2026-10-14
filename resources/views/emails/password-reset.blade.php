<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset your Arete Performance password</title>
    <style>
        @media only screen and (max-width:620px){.email-shell{width:100%!important}.email-pad{padding:26px 18px!important}.email-title{font-size:25px!important}.cta{display:block!important;text-align:center!important}.footer-pad{padding:22px 18px!important}}
    </style>
</head>
<body style="margin:0;padding:0;background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;color:#171717;-webkit-text-size-adjust:100%;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f3f6">
    <tr>
        <td align="center" style="padding:28px 12px">
            <table role="presentation" class="email-shell" width="620" cellspacing="0" cellpadding="0" style="width:620px;max-width:100%;background:#fff;border:1px solid #e3e6eb;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(18,25,38,.08)">
                <tr>
                    <td align="center" style="background:#080808;padding:25px 24px;border-bottom:3px solid #e9a611">
                        <img src="{{ $logoUrl }}" width="190" alt="Arete Performance" style="display:block;width:190px;max-width:72%;height:auto;border:0">
                    </td>
                </tr>
                <tr>
                    <td class="email-pad" style="padding:38px 42px 30px">
                        <div style="display:inline-block;padding:6px 10px;border-radius:20px;background:#fff3d5;color:#8c5e00;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em">Password reset</div>
                        <h1 class="email-title" style="margin:16px 0 10px;font-size:31px;line-height:1.18;color:#171717">Hi {{ $user->name }}, reset your password.</h1>
                        <p style="margin:0 0 24px;color:#656d79;font-size:15px;line-height:1.65">We received a request to reset the password for your Arete Performance admin account. Use the secure button below to create a new password.</p>
                        <p style="margin:0 0 24px"><a class="cta" href="{{ $resetUrl }}" style="display:inline-block;background:#e9a611;color:#111;padding:14px 23px;border-radius:9px;text-decoration:none;font-size:14px;font-weight:900">Reset password&nbsp; &rarr;</a></p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8f9fb;border:1px solid #e8ebef;border-radius:13px">
                            <tr>
                                <td style="padding:18px 20px;color:#69717d;font-size:13px;line-height:1.65">
                                    This link expires in 60 minutes. If you did not request this reset, you can safely ignore this email and your password will stay the same.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="footer-pad" style="padding:24px 42px;background:#0b0b0b;color:#aeb2ba;font-size:12px;line-height:1.6">
                        <strong style="display:block;color:#fff;font-size:14px;margin-bottom:4px">Arete Performance</strong>
                        Premium performance solutions designed to help you reach your full potential.
                        <div style="margin-top:12px"><a href="{{ route('frontend.shop') }}" style="color:#e9a611;text-decoration:none">Shop products</a>&nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href="{{ url('/') }}" style="color:#e9a611;text-decoration:none">Visit website</a></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
