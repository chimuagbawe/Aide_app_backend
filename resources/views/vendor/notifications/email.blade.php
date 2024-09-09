<div style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table
        style="max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <thead>
            <tr>
                <th style="text-align: left;">
                    <img src="https://i.imgur.com/xENdEZw.jpeg" width="90" height="90" alt="Company Logo"
                        style="max-width: 150px; margin-bottom: 20px; border-radius: 90px;">
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <h2 class="sarala-bold"
                        style="text-align: center; color: #333; font-size: 24px; margin-bottom: 10px;">Password Reset 🔏
                    </h2>
                    <p class="sarala-bold" style="font-size: 16px; color: #666; margin-left: 20px;">Hi dear,</p>
                    <p class="sarala-regular" style="font-size: 16px; color: #666; margin-left: 20px;">
                        Please click the button below to reset your password.
                    </p>
                    <a href="{{ $actionUrl }}"
                        style="display: inline-block; background-color: black; color: #fff; padding: 10px 20px; border-radius: 22px; text-decoration: none; font-size: 13.5px; margin-top: 10px; margin-left: 20px; cursor: pointer; border: 2px solid #fff;">
                        Reset Password
                    </a>

                    <p class="sarala-regular"
                        style="font-size: 14px; color: #999; margin-top: 20px; margin-left: 20px;">
                        This password reset link will expire in 60 minutes.
                    </p>
                    <p class="sarala-regular"
                        style="font-size: 14px; color: #999; margin-top: 20px; margin-left: 20px; text-decoration: none;">
                        If you did not request a password reset, no further action is required.
                    </p>
                </td>
            </tr>
            <tr>
                <td class="sarala-regular"
                    style="border-top: 1px solid #eee; padding-top: 20px; text-align: center; color: #999;">
                    <p class="sarala-regular"
                        style="font-size: 14px; color: #999; margin-top: 20px; margin-left: 20px;">
                        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into
                        your web browser: {{ $actionUrl }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
