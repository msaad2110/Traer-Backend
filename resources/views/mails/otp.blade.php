<p>Dear {{$user->first_name}},</p>
<p>As part of our commitment to ensuring the security of your account, we have received a request to reset the password associated with your account. In order to proceed with the password reset, please use the following One-Time Password (OTP):</p>
<b>{{$otp->token}}</b>

<p>Please be sure to enter this code within the designated field during the password reset process. Keep in mind that this OTP is valid for 10 minutes only and only 5 attempts are allowed at a time, so we recommend completing the reset as soon as possible.</p>

<p>Best regards,</p>
<p>Traer</p>