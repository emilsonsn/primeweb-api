@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Email Template</title>

<style>
    .header{
        background-color:#3e84b5 !important;
        color: white !important;        
    }

    .header a{
        color: white !important;        
    }

</style>

</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 14px; color: #333;">
    <table width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600px" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-collapse: collapse; border: 1px solid #cccccc;">
                    <!-- Header -->
                    <tr>
                        <td class="header" style="padding: 20px; text-align: center; font-size: 16px;">
                            <p>agenciaprimeweb.com.br | Tel: {{ $phone }}</p>
                        </td>
                    </tr>
                    <!-- Logo and introduction -->
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <img src="{{ url('images/logo.jpeg') }}" alt="PrimeWeb Logo" style="width: 120px; height: auto;">
                            <p style="margin-top: 20px;">Prezado(a) {{ $clientName }}</p>
                        </td>
                    </tr>
                    <!-- Meeting details -->
                    <tr>
                        <td style="padding: 20px; text-align: center; border-top: 2px dashed #ccc;">
                            <p>Segue link para reunião conforme combinado para data de {{ Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time)->format('d/m/Y H:i:s') }}</p>
                            <a href="{{ $url }}" style="display: inline-block; margin: 20px 0; padding: 10px 25px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Link reunião</a>
                            <p><strong>Lembrando que nosso único compromisso é você estar presente no dia e horário da reunião.</strong></p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 10px; background-color: #3e84b5; color: white; text-align: center; font-size: 12px;">
                            Atenciosamente,<br>Equipe Prime Web
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
