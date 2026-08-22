<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedConnect - Cuidado médico de excelência para você</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/meu-projeto-web/public/css/home.css">
</head>
<body>

    <header>
        <div class="logo">
            <div class="logo-icon">M</div>
            <span>MedConnect</span>
        </div>
        <nav>
            <a href="/meu-projeto-web/public/home">Início</a>
            <a href="#especialidades">Especialidades</a>
            <a href="#exames">Exames</a>
            <a href="#contato">Contato</a>
        </nav>
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="/meu-projeto-web/public/logout" class="btn btn-dark">Sair</a>
        <?php else: ?>
            <a href="/meu-projeto-web/public/login" class="btn btn-dark">Entrar</a>
        <?php endif; ?>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <div class="badge-excelencia">🛡️ Excelência em saúde desde 2008</div>
                <h1>Cuidado médico <br><span>de excelência</span> <br>para você.</h1>
                <p>Especialistas de alto nível, tecnologia de ponta e atendimento humanizado em um único lugar.</p>
                
                <a href="/meu-projeto-web/public/login" class="btn btn-primary">
                    Agendar com atendente ➔
                </a>

                <div class="stats">
                    <div class="stat-item">
                        <h3>12+</h3>
                        <p>Especialidades</p>
                    </div>
                    <div class="stat-item">
                        <h3>8.400+</h3>
                        <p>Pacientes</p>
                    </div>
                    <div class="stat-item">
                        <h3>98%</h3>
                        <p>Satisfação</p>
                    </div>
                </div>
            </div>

            <div class="hero-image-container">
                <img class="hero-img" src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&q=80&w=600" alt="Médico utilizando smartphone">
                <div class="floating-badge">
                    <div class="badge-icon">⚡</div>
                    <div>
                        <p>Monitoramento 24h</p>
                        <span>Suporte contínuo ao paciente</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="especialidades" class="section-container">
            <span class="section-tag">Especialidades</span>
            <h2 class="section-title">Cuidado especializado para cada necessidade</h2>
            
            <div class="grid-layout cards-grid">
                <article class="card">
                    <div class="card-icon">💙</div>
                    <h4>Cardiologia</h4>
                    <p>Saúde cardiovascular avançada</p>
                </article>
                <article class="card">
                    <div class="card-icon">🧸</div>
                    <h4>Pediatria</h4>
                    <p>Cuidado integral para crianças</p>
                </article>
                <article class="card">
                    <div class="card-icon">🧠</div>
                    <h4>Neurologia</h4>
                    <p>Diagnóstico e tratamento neurológico</p>
                </article>
                <article class="card">
                    <div class="card-icon">🦴</div>
                    <h4>Ortopedia</h4>
                    <p>Medicina esportiva e reabilitação</p>
                </article>
                <article class="card">
                    <div class="card-icon">👁️</div>
                    <h4>Oftalmologia</h4>
                    <p>Saúde ocular completa</p>
                </article>
                <article class="card">
                    <div class="card-icon">🩺</div>
                    <h4>Clínica Geral</h4>
                    <p>Atendimento integral ao paciente</p>
                </article>
            </div>
        </section>

        <section id="exames" class="section-container bg-white">
            <span class="section-tag">Exames</span>
            <h2 class="section-title">Laboratório e Diagnóstico por Imagem</h2>
            
            <div class="grid-layout exames-grid">
                <div class="exame-badge"><span>🔬</span> Hemograma Completo</div>
                <div class="exame-badge"><span>🫀</span> Eletrocardiograma</div>
                <div class="exame-badge"><span>🌀</span> Tomografia</div>
                <div class="exame-badge"><span>🧲</span> Ressonância Magnética</div>
                <div class="exame-badge"><span>🔊</span> Ultrassonografia</div>
                <div class="exame-badge"><span>📊</span> Ecocardiograma</div>
                <div class="exame-badge"><span>🦴</span> Densitometria Óssea</div>
                <div class="exame-badge"><span>🛡️</span> Colonoscopia</div>
            </div>
        </section>

        <section id="contato" class="section-container">
            <h2>Pronto para agendar sua consulta?</h2>
            <p class="text-muted text-margin">Entre em contato agora e cuide da sua saúde com os melhores especialistas.</p>
            <a href="/meu-projeto-web/public/login" class="btn btn-primary">Agendar Agora ➔</a>
        </section>
    </main>

    <footer>
        <div class="logo">
            <div class="logo-icon logo-icon-accent">M</div>
            <span>MedConnect</span>
        </div>
        
        <div class="footer-info">
            <div class="info-item"><span>📞</span> (74) 988XX-02XX</div>
            <div class="info-item"><span>📍</span> Praça da Catedral, 228, Centro — Juazeiro</div>
        </div>

        <div class="copyright">
            © 2026 MedConnect. Todos os direitos reservados.
        </div>
    </footer>

</body>
</html>