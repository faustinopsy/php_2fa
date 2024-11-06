<?php
return [
    'db' => [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/../database/database.sqlite',
    ],
    'mail' => [
        'host' => 'smtp.gmail.com',
        'username' => 'usuario@gmail.com',
        'password' => 'senha',
        'port' => 587,
        'encryption' => 'tls',
    ],
];

/*
O Gmail implementa medidas de segurança rigorosas para proteger as contas dos usuários. Por padrão, o Gmail bloqueia tentativas de login de aplicativos que não usam padrões modernos de segurança.

Para enviar e-mails usando o Gmail SMTP com o PHPMailer, você precisa:

Ativar a Verificação em Duas Etapas na sua conta do Google.
Gerar uma Senha de Aplicativo específica para sua aplicação.
Passos para Configurar o Gmail para SMTP
1. Ativar a Verificação em Duas Etapas na Sua Conta do Google
Acesse as Configurações de Segurança da Conta Google.
Encontre a seção "Como fazer login no Google".
Clique em "Verificação em duas etapas" e siga as instruções para ativá-la.
2. Gerar uma Senha de Aplicativo
Após ativar a verificação em duas etapas, retorne à seção "Segurança".
Clique em "Senhas de app". (https://myaccount.google.com/u/2/apppasswords)
Talvez seja necessário fazer login novamente.
Em "Selecionar o app e o dispositivo", escolha "Outro (nome personalizado)".
Insira um nome para o app, por exemplo, "PHPMailer", e clique em "Gerar".
O Google irá gerar uma senha de 16 caracteres. Copie essa senha, pois você precisará dela para a configuração SMTP.
*/