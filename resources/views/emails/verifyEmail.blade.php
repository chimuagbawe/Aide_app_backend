<div style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <table
        style="
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        ">
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
                        style="text-align: center; color: #333; font-size: 24px; margin-bottom: 10px;">
                        Verify Your Email ✅
                    </h2>
                    <p class="sarala-bold" style="font-size: 16px; color: #666; margin-left: 20px;">
                        Hi {{ $user->name ? explode(' ', $user->name)[0] . ',' : 'dear,' }}
                    </p>
                    <p class="sarala-regular" style="font-size: 16px; color: #666; margin-left: 20px;">
                        Please confirm your email address to complete your registration. Click the button below to
                        verify your
                        email:
                    </p>
                    <a class="sarala-regular" href="{{ $url }}"
                        style="
                            display: inline-block;
                            background-color: black;
                            color: #fff;
                            padding: 10px 20px;
                            border-radius: 22px;
                            text-decoration: none;
                            font-size: 13.5px;
                            margin-top: 10px;
                            cursor: pointer;
                            margin-left: 20px;
                            border: 2px solid #fff;
                        ">
                        Verify Email
                    </a>
                    <p class="sarala-regular"
                        style="font-size: 14px; color: #999; margin-left: 20px; margin-top: 20px;">
                        If you didn’t create an account with us, please ignore this email.
                    </p>
                </td>
            </tr>
            <tr>
                <td class="sarala-regular"
                    style="border-top: 1px solid #eee; padding-top: 20px; text-align: center; color: #999;">
                    <p>Aide | 37 Farm Road Estate, Port-Harcourt, Nigeria</p>
                    <p>© 2024 Aide. All rights reserved.</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
