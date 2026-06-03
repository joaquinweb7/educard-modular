<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado Enviado</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 5px;">
        <tr>
            <td
                style="padding: 20px; text-align: center; background-color: #007bff; color: #ffffff; border-radius: 5px 5px 0 0;">
                <h1 style="margin: 0; font-size: 24px;">¡Certificado Enviado!</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p style="font-size: 16px;">Estimado/a {{ $certificado_nombre }},</p>
                <p style="font-size: 16px;">Nos complace informarle que hemos enviado su certificado del curso:</p>
                <p style="font-size: 18px; font-weight: bold; color: #007bff; text-align: center;">"{{ $certificado_curso }}"
                </p>
                <p style="font-size: 16px;">El certificado ha sido enviado como archivo adjunto a este correo
                    electrónico. Por favor, revise los archivos adjuntos para acceder a su certificado.</p>
                <p style="font-size: 16px;">Si tiene alguna dificultad para acceder al certificado o si tiene alguna
                    pregunta, no dude en contactarnos.</p>
                <p style="font-size: 16px;">¡Felicitaciones por completar el curso y gracias por confiar en nuestra
                    institución!</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f1f3f5; border-radius: 0 0 5px 5px;">
                <p style="font-size: 14px; color: #6c757d; margin: 0;">Este es un correo automático, por favor no
                    responda a este mensaje.</p>
            </td>
        </tr>
    </table>
</body>

</html>
