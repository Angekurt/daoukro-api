<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fiche non retenue</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f0; margin: 0; padding: 24px; }
    .card { background: #fff; border-radius: 16px; max-width: 540px; margin: 0 auto; overflow: hidden; }
    .header { background: #b91c1c; padding: 28px 32px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .body { padding: 28px 32px; }
    .body p { color: #3d3d3a; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
    .badge { display: inline-block; background: #fee2e2; color: #b91c1c; font-weight: 600;
             border-radius: 20px; padding: 6px 16px; font-size: 14px; margin-bottom: 20px; }
    .motif { background: #fef9ec; border-left: 3px solid #f59e0b;
             padding: 12px 16px; border-radius: 8px; margin: 16px 0; }
    .motif p { color: #78350f; font-size: 14px; margin: 0; }
    .cta { display: block; background: #145217; color: #fff; text-decoration: none;
           text-align: center; padding: 14px 24px; border-radius: 12px;
           font-weight: 600; font-size: 15px; margin-top: 24px; }
    .footer { padding: 16px 32px; border-top: 1px solid #e3e2da; }
    .footer p { color: #8e918a; font-size: 12px; margin: 0; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>Daoukro Digital</h1>
    </div>
    <div class="body">
      <p>Bonjour <strong>{{ $nomDestinataire }}</strong>,</p>
      <p>Votre fiche a été examinée par la mairie de Daoukro mais n'a pas pu être publiée dans l'application.</p>
      <div class="badge">{{ $typeFiche }} — {{ $nomFiche }}</div>
      @if($motif)
      <div class="motif">
        <p><strong>Motif :</strong> {{ $motif }}</p>
      </div>
      @endif
      <p>Vous pouvez corriger votre fiche et la soumettre à nouveau depuis le portail Daoukro Pro.</p>
      <a href="https://daoukro-pro.akdev.tech" class="cta">Modifier ma fiche</a>
    </div>
    <div class="footer">
      <p>Cet email a été envoyé automatiquement par Daoukro Digital. Ne pas répondre à ce message.</p>
    </div>
  </div>
</body>
</html>
