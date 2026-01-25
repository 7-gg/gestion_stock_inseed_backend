<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de connexion</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        h1 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .code-section {
            background-color: #f0f7ff;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }

        .code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #007bff;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
        }

        .expiration {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Connexion sécurisée</h1>
            <p>Votre code de connexion</p>
        </div>

        <div class="greeting">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
            <p>Nous avons reçu une demande de connexion à votre compte. Utilisez le code ci-dessous pour accéder à votre espace.</p>
        </div>

        <div class="code-section">
            <p style="margin: 0 0 10px 0; color: #666;">Votre code de connexion</p>
            <div class="code">{{ $code }}</div>
            <div class="expiration">
                ⏱️ Ce code expire dans <strong>{{ $expirationMinutes }} minutes</strong>
            </div>
        </div>

        <div class="warning">
            ⚠️ <strong>Important :</strong> Ne partagez jamais ce code avec quiconque. Nos équipes ne vous demanderont jamais votre code de connexion.
        </div>

        <p style="color: #666; font-size: 14px;">
            Si vous n'avez pas demandé cette connexion, ignorez simplement cet email. Votre compte reste sécurisé.
        </p>

        <div class="footer">
            <p>© {{ date('Y') }} Gestion de Stock. Tous droits réservés.</p>
            <p>Ceci est un email automatisé. Veuillez ne pas y répondre.</p>
        </div>
    </div>
</body>

</html>