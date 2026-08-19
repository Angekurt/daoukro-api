<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fiche publiée</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f0; margin: 0; padding: 24px; }
    .card { background: #fff; border-radius: 16px; max-width: 540px; margin: 0 auto; overflow: hidden; }
    .header { background: #145217; padding: 28px 32px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .body { padding: 28px 32px; }
    .body p { color: #3d3d3a; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
    .badge { display: inline-block; background: #e6efe6; color: #145217; font-weight: 600;
             border-radius: 20px; padding: 6px 16px; font-size: 14px; margin-bottom: 20px; }
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
      <p>Bonne nouvelle ! Votre fiche a été validée par la mairie de Daoukro et est maintenant visible dans l'application mobile.</p>
      <div class="badge">{{ $typeFiche }} — {{ $nomFiche }}</div>
      <p>Les utilisateurs de l'app peuvent désormais vous trouver, vous contacter et laisser des avis sur votre fiche.</p>
      <p>Merci de contribuer au développement numérique de Daoukro.</p>
      <a href="https://daoukro.akdev.ci" class="cta">Voir l'application</a>
    </div>
    <div class="footer">
      <p>Cet email a été envoyé automatiquement par Daoukro Digital. Ne pas répondre à ce message.</p>
    </div>
  </div>
</body>
</html>
