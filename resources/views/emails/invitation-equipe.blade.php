<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Invitation équipe</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f0; margin: 0; padding: 24px; }
    .card { background: #fff; border-radius: 16px; max-width: 540px; margin: 0 auto; overflow: hidden; }
    .header { background: #145217; padding: 28px 32px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .body { padding: 28px 32px; }
    .body p { color: #3d3d3a; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
    .badge { display: inline-block; background: #e6efe6; color: #145217; font-weight: 600;
             border-radius: 20px; padding: 6px 16px; font-size: 14px; margin-bottom: 20px; }
    .role-badge { display: inline-block; background: #fef9ec; color: #78350f; font-weight: 600;
                  border-radius: 20px; padding: 4px 12px; font-size: 13px; margin-left: 8px; }
    .cta { display: block; background: #145217; color: #fff; text-decoration: none;
           text-align: center; padding: 14px 24px; border-radius: 12px;
           font-weight: 600; font-size: 15px; margin-top: 24px; }
    .expire { color: #8e918a; font-size: 13px; margin-top: 12px; text-align: center; }
    .footer { padding: 16px 32px; border-top: 1px solid #e3e2da; }
    .footer p { color: #8e918a; font-size: 12px; margin: 0; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>Daoukro Pro</h1>
    </div>
    <div class="body">
      <p>Bonjour,</p>
      <p>
        <strong>{{ $nomInviteur }}</strong> vous invite à rejoindre l'équipe
        <strong>{{ $nomEquipe }}</strong> sur Daoukro Pro.
      </p>
      <div>
        <span class="badge">{{ $nomEquipe }}</span>
        <span class="role-badge">{{ $role === 'manager' ? 'Manager' : 'Éditeur' }}</span>
      </div>
      <p>
        En tant que <strong>{{ $role === 'manager' ? 'Manager' : 'Éditeur' }}</strong>, vous pourrez
        {{ $role === 'manager' ? 'créer, modifier et gérer toutes les fiches de l\'équipe.' : 'modifier les fiches existantes de l\'équipe.' }}
      </p>
      <a href="{{ $urlAcceptation }}" class="cta">Accepter l'invitation</a>
      <p class="expire">Cette invitation expire dans 7 jours.</p>
    </div>
    <div class="footer">
      <p>Si vous n'attendiez pas cette invitation, ignorez cet email. Aucun compte ne sera créé sans votre action.</p>
    </div>
  </div>
</body>
</html>
