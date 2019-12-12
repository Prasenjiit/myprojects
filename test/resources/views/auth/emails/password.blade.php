<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  <div>
  <table align="left" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
      <td>
        <table cellspacing="0"  cellpadding="0">
          <tbody>
          <tr>
            <td>
              <table cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                  <td width="250">
                    <a target="_blank" href="http://toptechinfo.net/dms/public/login">
                      <img src="http://toptechinfo.net/dms/public/images/logo/<?php echo Session::get('settings_logo');?>" width="100px" alt="logo"></a>
                  </td>
                  <td width="250" id="m_-8524342663107919999m_-5472023132891936660title" valign="top" align="right"><p><b>DMS</b>:Reset Password</p></td>
                </tr>
                </tbody>
              </table>
            </td>
          </tr>

          <tr>
            <td id="m_-8524342663107919999m_-5472023132891936660verificationMsg">
              <p>Dear {{ $user->user_full_name }},</p></br>
              <p>You have requested to change the password on your account.If you requested this password change, please set a new password by the following link below:</p></br>
              <p><a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a></p></br>
              <br>
            </td>
          </tr>

          <tr>
            <td id="m_-8524342663107919999m_-5472023132891936660accountSecurity">
              <p>​Warning: The password reset token expires after one hour.</p></br>
              <p>If you don't want to change your password, just ignore this message.</p>
            </td>
          </tr>

          <tr>
            <td id="m_-8524342663107919999m_-5472023132891936660closing">
              <p>Thanks! <br> <span class="m_-8524342663107919999m_-5472023132891936660signature"><?php echo Session::get('settings_company_name');?></span>
              </p>
            </td>
          </tr>
          </tbody>
        </table>
      </td>
    </tr>
    </tbody>
  </table>

</body>
</html>

