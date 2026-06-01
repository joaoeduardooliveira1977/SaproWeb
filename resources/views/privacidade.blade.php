<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade — Software Jurídico</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.7; }
        .header { background: #0f2540; padding: 20px 0; }
        .header-inner { max-width: 800px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 18px; font-weight: 700; color: #fff; }
        .header a { color: #93c5fd; font-size: 13px; text-decoration: none; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 24px 60px; }
        .card { background: #fff; border-radius: 16px; padding: 40px 48px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        h2 { font-size: 26px; font-weight: 800; color: #0f2540; margin-bottom: 6px; }
        .sub { font-size: 13px; color: #64748b; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9; }
        h3 { font-size: 15px; font-weight: 700; color: #0f2540; margin: 28px 0 10px; }
        p, li { font-size: 14px; color: #475569; margin-bottom: 10px; }
        ul { padding-left: 20px; margin-bottom: 10px; }
        li { margin-bottom: 6px; }
        .badge { display: inline-block; background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 99px; letter-spacing: .5px; margin-bottom: 20px; }
        .footer-note { margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8; text-align: center; }
        @media(max-width:600px) { .card { padding: 24px 20px; } }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <h1>⚖️ Software Jurídico</h1>
        <a href="{{ route('login') }}">← Voltar ao sistema</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <span class="badge">LGPD — Lei 13.709/2018</span>
        <h2>Política de Privacidade</h2>
        <p class="sub">Última atualização: {{ now()->format('d/m/Y') }} &nbsp;·&nbsp; Software Jurídico — Sistema de Gestão para Escritórios de Advocacia</p>

        <h3>1. Quem somos</h3>
        <p>O Software Jurídico é um sistema de gestão desenvolvido para escritórios de advocacia, disponibilizado em modelo SaaS (Software como Serviço). Cada escritório ("tenant") é responsável pelo tratamento dos dados pessoais de seus próprios clientes dentro da plataforma.</p>

        <h3>2. Dados que coletamos</h3>
        <ul>
            <li><strong>Dados cadastrais de clientes:</strong> nome, CPF/CNPJ, RG, data de nascimento, endereço, telefone, e-mail.</li>
            <li><strong>Dados processuais:</strong> número do processo, andamentos, documentos, prazos e audiências.</li>
            <li><strong>Dados financeiros:</strong> honorários, recebimentos, inadimplência e histórico de pagamentos.</li>
            <li><strong>Dados de acesso:</strong> login, e-mail, endereço IP e data/hora de acesso dos usuários do sistema.</li>
        </ul>

        <h3>3. Finalidade do tratamento</h3>
        <ul>
            <li>Gestão e acompanhamento de processos jurídicos.</li>
            <li>Controle financeiro e de honorários advocatícios.</li>
            <li>Comunicação com clientes e partes envolvidas nos processos.</li>
            <li>Cumprimento de obrigações legais pelos escritórios de advocacia.</li>
            <li>Segurança, prevenção a fraudes e auditoria de acesso.</li>
        </ul>

        <h3>4. Base legal para o tratamento</h3>
        <p>O tratamento de dados é realizado com base nas seguintes hipóteses previstas na LGPD:</p>
        <ul>
            <li><strong>Execução de contrato</strong> (Art. 7º, V): gestão da relação advocatícia.</li>
            <li><strong>Legítimo interesse</strong> (Art. 7º, IX): segurança e melhoria do serviço.</li>
            <li><strong>Cumprimento de obrigação legal</strong> (Art. 7º, II): obrigações do Estatuto da OAB e legislação processual.</li>
            <li><strong>Consentimento</strong> (Art. 7º, I): quando aplicável.</li>
        </ul>

        <h3>5. Seus direitos como titular (Art. 18 da LGPD)</h3>
        <p>Você tem direito a:</p>
        <ul>
            <li>Confirmar se seus dados são tratados pelo escritório.</li>
            <li>Acessar seus dados pessoais armazenados.</li>
            <li>Corrigir dados incompletos, inexatos ou desatualizados.</li>
            <li>Solicitar a anonimização, bloqueio ou eliminação de dados desnecessários.</li>
            <li>Solicitar a portabilidade dos seus dados.</li>
            <li>Revogar o consentimento a qualquer momento.</li>
        </ul>
        <p>Para exercer esses direitos, entre em contato diretamente com o escritório de advocacia responsável pelo seu atendimento.</p>

        <h3>6. Compartilhamento de dados</h3>
        <p>Seus dados <strong>não são vendidos ou compartilhados</strong> com terceiros para fins comerciais. Podem ser compartilhados apenas:</p>
        <ul>
            <li>Com autoridades judiciais e administrativas, quando exigido por lei.</li>
            <li>Com prestadores de serviço técnico (hospedagem, e-mail) sob contratos de confidencialidade.</li>
        </ul>

        <h3>7. Segurança</h3>
        <p>Adotamos medidas técnicas e organizacionais para proteger seus dados, incluindo: criptografia de senhas, controle de acesso por perfil, autenticação em dois fatores para administradores, registro de auditoria de todas as ações e isolamento de dados por escritório (multi-tenant).</p>

        <h3>8. Retenção dos dados</h3>
        <p>Os dados são mantidos pelo tempo necessário para o cumprimento das finalidades descritas e das obrigações legais aplicáveis. Após o encerramento do contrato com o escritório, os dados são eliminados em até 90 dias, salvo obrigação legal de retenção.</p>

        <h3>9. Contato e DPO</h3>
        <p>Para dúvidas sobre esta política ou para exercer seus direitos, entre em contato com o escritório de advocacia responsável pelo seu processo. Para questões relativas à plataforma em si, entre em contato pelo sistema.</p>

        <div class="footer-note">
            Este documento foi elaborado em conformidade com a Lei nº 13.709/2018 (LGPD) e suas regulamentações da ANPD.<br>
            Software Jurídico &copy; {{ date('Y') }} — Todos os direitos reservados.
        </div>
    </div>
</div>

</body>
</html>
