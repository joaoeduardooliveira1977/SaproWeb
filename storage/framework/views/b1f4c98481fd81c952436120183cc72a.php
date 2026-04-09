<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SISTEMA JURÍDICO</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(160deg, #060f1e 0%, #0d2040 50%, #152e52 100%);
            padding: 32px 24px;
        }

        /* ══ CONTAINER PRINCIPAL ══ */
        .container {
            display: flex;
            align-items: center;
            gap: 56px;
            width: 100%;
            max-width: 1140px;
        }

        /* ══ ESQUERDA ══ */
        .esquerda {
            flex: 1;
            min-width: 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 99px;
            padding: 7px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 28px;
        }

        .esquerda h1 {
            font-size: 42px;
            font-weight: 800;
            color: #fff;
            line-height: 1.18;
            margin-bottom: 18px;
            letter-spacing: -.5px;
        }

        .esquerda h1 em { font-style: normal; color: #60a5fa; }

        .esquerda > p {
            font-size: 15px;
            color: #8898aa;
            line-height: 1.75;
            margin-bottom: 32px;
            max-width: 480px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .fcard {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 12px;
            padding: 18px 16px;
            transition: background .2s;
        }

        .fcard:hover { background: rgba(255,255,255,.09); }

        .fcard-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            margin-bottom: 10px;
        }

        .fcard h3 { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 5px; }
        .fcard p  { font-size: 12px; color: #5a7090; line-height: 1.5; margin: 0; }

        .tags { display: flex; gap: 10px; flex-wrap: wrap; }

        .tag {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 99px;
            padding: 6px 14px;
            font-size: 12px;
            color: #7a90a8;
            font-weight: 500;
        }

        /* ══ DIREITA ══ */
        .direita {
            width: 420px;
            flex-shrink: 0;
        }

        .form-card {
            background: #fff;
            border-radius: 20px;
            padding: 44px 40px;
            box-shadow: 0 32px 80px rgba(0,0,0,.45);
        }

        .logo-wrap { text-align: center; margin-bottom: 32px; }

        .logo-icon {
            width: 64px; height: 64px;
            border-radius: 16px;
            background: #eff6ff;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            font-size: 28px;
        }

        .logo-wrap h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0d1f3c;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .logo-wrap p { font-size: 13px; color: #64748b; line-height: 1.55; }

        .lbl {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 7px;
        }

        .inp {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: #fafafa;
        }

        .inp:focus {
            border-color: #2563a8;
            box-shadow: 0 0 0 3px rgba(37,99,168,.1);
            background: #fff;
        }

        .fg { margin-bottom: 16px; }

        .frow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .check-lbl {
            display: flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
        }

        .check-lbl input { accent-color: #2563a8; width: 14px; height: 14px; }

        .btn-entrar {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #0d2040, #2563a8);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .5px;
            transition: opacity .2s, transform .1s;
        }

        .btn-entrar:hover { opacity: .9; transform: translateY(-1px); }

        .err {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 16px;
        }

        .foot { text-align: center; margin-top: 18px; font-size: 13px; color: #64748b; }
        .foot a { color: #2563a8; font-weight: 700; text-decoration: none; }
        .foot a:hover { text-decoration: underline; }

        .version { text-align: center; margin-top: 24px; font-size: 11px; color: #94a3b8; }

        @media (max-width: 900px) {
            .container { flex-direction: column; gap: 32px; }
            .esquerda { text-align: center; }
            .esquerda > p { margin-left: auto; margin-right: auto; }
            .tags { justify-content: center; }
            .direita { width: 100%; max-width: 420px; }
        }

        @media (max-width: 480px) {
            .features { grid-template-columns: 1fr; }
            .form-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
<div class="container">

    
    <div class="esquerda">
        <div class="badge">⚖️ Sistema Jurídico Web</div>

        <h1>Seu escritório mais organizado, <em>produtivo</em> e profissional desde o login.</h1>

        <p>Centralize processos, clientes, prazos, honorários e indicadores financeiros em um só sistema. Um ambiente moderno para acompanhar a operação com mais controle e agilidade.</p>

        <div class="features">
            <div class="fcard">
                <div class="fcard-icon" style="background:rgba(245,158,11,.15);">📁</div>
                <h3>Gestão de Processos</h3>
                <p>Acompanhe andamentos, responsáveis, prazos e informações essenciais de cada caso.</p>
            </div>
            <div class="fcard">
                <div class="fcard-icon" style="background:rgba(16,185,129,.15);">💰</div>
                <h3>Financeiro Integrado</h3>
                <p>Controle honorários, recebimentos, despesas e inadimplência em uma visão executiva.</p>
            </div>
            <div class="fcard">
                <div class="fcard-icon" style="background:rgba(99,102,241,.15);">📊</div>
                <h3>Painel Inteligente</h3>
                <p>Visualize indicadores importantes do escritório com uma interface clara e profissional.</p>
            </div>
        </div>

        <div class="tags">
            <span class="tag">🔒 Acesso seguro</span>
            <span class="tag">⚡ Rotina mais ágil</span>
            <span class="tag">📋 Tudo centralizado</span>
        </div>
    </div>

    
    <div class="direita">
        <div class="form-card">

            <div class="logo-wrap">
                <div class="logo-icon">⚖️</div>
                <h2>SISTEMA JURÍDICO</h2>
                <p>Sistema de acompanhamento de processos com controle moderno e visão profissional.</p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="err"><?php echo e($errors->first()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="err"><?php echo e(session('error')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="fg">
                    <label class="lbl">Usuário</label>
                    <input type="text" name="login" class="inp"
                        placeholder="Seu login"
                        value="<?php echo e(old('login')); ?>" required autofocus>
                </div>

                <div class="fg">
                    <label class="lbl">Senha</label>
                    <input type="password" name="senha" class="inp"
                        placeholder="Sua senha" required>
                </div>

                <div class="frow">
                    <label class="check-lbl">
                        <input type="checkbox" name="lembrar" value="1" <?php echo e(old('lembrar') ? 'checked' : ''); ?>>
                        Manter conectado
                    </label>
                    <a href="#" style="font-size:13px;color:#2563a8;font-weight:600;text-decoration:none;">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn-entrar">Entrar no Sistema</button>
            </form>

            <div class="foot">
                Não tem conta? <a href="<?php echo e(route('registro')); ?>">Criar conta grátis</a>
            </div>

            <div class="version">SISTEMA JURÍDICO — versão 1.0</div>
        </div>
    </div>

</div>
</body>
</html>
<?php /**PATH C:\projetos\saproweb-base\resources\views/auth/login.blade.php ENDPATH**/ ?>