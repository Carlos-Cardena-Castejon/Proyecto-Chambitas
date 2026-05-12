<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <title>Chambitas | Conecta con trabajos y servicios</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        :root {
            --primary: #FFD700;
            --bg: #050505;
            --card-bg: #111111;
            --text-gray: #a0a0a0;
            --border: rgba(255, 255, 255, 0.08);
        }

        html { scroll-behavior: smooth; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            color: #fff; 
            margin: 0; 
            overflow-x: hidden; 
        }

        /* --- NAVEGACIÓN --- */
        nav {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 15px 8%; 
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(15px); 
            position: fixed; 
            width: 100%; 
            top: 0;
            z-index: 1000; 
            box-sizing: border-box; /* Evita que el padding desborde el ancho */
            border-bottom: 1px solid var(--border);
        }

        .logo-img { 
            max-height: 45px; 
            width: auto; 
            transition: 0.3s;
        }

        .nav-links { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }

        .nav-links a { 
            color: #fff; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.8rem; 
            padding: 10px 15px; 
            transition: 0.3s; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }

        .nav-links a:hover { color: var(--primary); }

        .btn-register { 
            background: var(--primary); 
            color: #000 !important; 
            border-radius: 12px; 
            margin-left: 10px; 
            font-weight: 800; 
        }
        
        /* --- HAMBURGUESA --- */
        .hamburger {
            display: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #fff;
            z-index: 1001;
            position: relative;
        }

        /* --- RESPONSIVE NAVBAR --- */
        @media (max-width: 900px) {
            nav {
                padding: 10px 20px; /* Padding reducido en móvil */
                height: 70px;
            }

            .logo-img {
                max-height: 32px; /* Logo más pequeño en móvil */
            }

            .hamburger { 
                display: block; 
            }

            .nav-links { 
                position: fixed;
                top: 0; 
                right: -100%; /* Totalmente fuera de pantalla */
                height: 100vh;
                width: 260px; 
                flex-direction: column; 
                align-items: center; 
                justify-content: center;
                padding: 40px; 
                gap: 25px;
                transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                background: #000;
                z-index: 1000;
                box-shadow: -10px 0 30px rgba(0,0,0,0.5);
            }

            .nav-links.active { 
                right: 0; 
            }

            .nav-links a {
                font-size: 1rem;
                width: 100%;
                text-align: center;
            }

            .btn-register {
                margin-left: 0;
            }
        }

        /* --- HERO --- */
        .hero {
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-align: center;
            background: radial-gradient(circle at center, #1a1a1a 0%, #050505 100%);
            padding: 0 10%; 
            position: relative;
        }

        .hero-content h1 { 
            font-size: 4rem; 
            font-weight: 900; 
            letter-spacing: -3px; 
            margin-bottom: 20px; 
            line-height: 1; 
        }

        .hero-content h1 span { 
            color: var(--primary); 
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.3); 
        }

        .hero-content p { 
            font-size: 1.2rem; 
            color: var(--text-gray); 
            max-width: 650px; 
            margin: 0 auto 40px; 
        }

        .btn-more { 
            background: #fff; 
            color: #000; 
            padding: 14px 35px; 
            border-radius: 15px; 
            text-decoration: none; 
            font-weight: 900; 
            font-size: 1rem; 
            text-transform: uppercase; 
            transition: 0.3s; 
            display: inline-block; 
            box-shadow: 0 10px 30px rgba(255,255,255,0.1); 
        }

        .btn-more:hover { 
            background: var(--primary); 
            transform: translateY(-3px); 
        }

        /* --- SECCIÓN CARRUSEL --- */
        .popular-section { padding: 80px 0; background: #080808; border-bottom: 1px solid var(--border); }
        .swiper { width: 100%; padding: 50px 0; }
        .swiper-slide { width: 220px; height: 300px; border-radius: 25px; border: 1px solid var(--border); background: #111; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: 0.5s; }
        .swiper-slide i { margin-bottom: 20px; color: var(--primary); filter: drop-shadow(0 0 10px rgba(255,215,0,0.2)); }
        .swiper-slide span { font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; color: #fff; }
        .swiper-slide-active { border: 2px solid var(--primary); transform: scale(1.05); background: #161616; }

        /* --- SECCIÓN CHAMBAS --- */
        .section { padding: 80px 8%; }
        .section-title { text-align: center; font-size: 2.5rem; font-weight: 900; margin-bottom: 40px; letter-spacing: -2px; }
        .section-title span { color: var(--primary); }
        .jobs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        
        .job-card { 
            background: var(--card-bg); 
            border-radius: 20px; 
            padding: 30px; 
            border: 1px solid var(--border); 
            position: relative; 
            overflow: hidden; 
            transition: 0.3s; 
        }

        .job-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .job-blur { filter: blur(12px); opacity: 0.15; }
        
        .job-overlay { 
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            display: flex; flex-direction: column; align-items: center; justify-content: center; 
            background: rgba(0,0,0,0.7); z-index: 10; backdrop-filter: blur(4px); 
        }

        .btn-unlock { 
            background: #fff; color: #000; padding: 12px 25px; border-radius: 12px; 
            text-decoration: none; font-weight: 900; font-size: 0.8rem; 
        }

        /* --- SECCIÓN NOSOTROS --- */
        .about-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 25px; margin-top: 40px; }
        .about-card { text-align: center; padding: 20px; background: #080808; border-radius: 15px; border: 1px solid var(--border); }
        .about-card i { font-size: 2rem; color: var(--primary); margin-bottom: 15px; }
        .about-card h3 { font-weight: 800; margin-bottom: 10px; }
        .about-card p { color: var(--text-gray); line-height: 1.5; font-size: 0.85rem; }

        /* --- FOOTER --- */
        footer { padding: 60px 8% 30px; background: #000; border-top: 1px solid var(--border); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; }
        .logo-img-footer { max-height: 50px; margin-bottom: 20px; }

        @media (max-width: 700px) {
            .hero-content h1 { font-size: 2.8rem; letter-spacing: -1px; }
            .footer-grid { grid-template-columns: 1fr; text-align: center; }
            .logo-img-footer { margin: 0 auto 20px; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="inicio.php"><img src="img/logo.png" alt="Logo" class="logo-img"></a>
        <div class="hamburger"><i class="fas fa-bars"></i></div>
        <div class="nav-links">
            <a href="#populares">Populares</a>
            <a href="#chambas">Proyectos</a>
            <a href="#nosotros">Nosotros</a>
            <a href="login.php" class="btn-login">Inicio Sesion</a>
            <a href="registro.php" class="btn-register">Registrarme</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Encuentra ayuda o trabajo en <span>Chambitas.</span></h1>
<p>Conectamos personas que necesitan un servicio con quienes pueden hacerlo. Publica, busca y contacta de forma sencilla dentro de tu comunidad.</p>
            <a href="#populares" class="btn-more">Conoce más</a>
        </div>
    </header>

    <section class="popular-section" id="populares">
        <h2 style="text-align:center; font-size: 2.2rem; font-weight: 800; margin-bottom: 10px;">Categorías <span>más solicitadas</span></h2>
        <p style="text-align:center; color: var(--text-gray); margin-bottom: 40px;">Servicios reales que las personas buscan </p>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><i class="fas fa-bolt fa-4x"></i><span>Electricidad</span></div>
                <div class="swiper-slide"><i class="fas fa-hammer fa-4x"></i><span>Construcción</span></div>
                <div class="swiper-slide"><i class="fas fa-faucet fa-4x"></i><span>Plomería</span></div>
                <div class="swiper-slide"><i class="fas fa-paint-roller fa-4x"></i><span>Pintura</span></div>
                <div class="swiper-slide"><i class="fas fa-broom fa-4x"></i><span>Limpieza</span></div>
                <div class="swiper-slide"><i class="fas fa-tools fa-4x"></i><span>Mecánica</span></div>
                <div class="swiper-slide"><i class="fas fa-laptop-code fa-4x"></i><span>Soporte TI</span></div>
                <div class="swiper-slide"><i class="fas fa-truck-ramp-box fa-4x"></i><span>Fletes</span></div>
            </div>
        </div>
    </section>

    <section class="section" id="chambas">
        <h2 class="section-title">Nuevas <span>Chambitas</span></h2>
        <div class="jobs-grid">
            <div class="job-card">
                <div class="job-overlay"><i class="fas fa-lock" style="color:var(--primary); font-size:2rem; margin-bottom:15px;"></i><a href="login.php" class="btn-unlock">Acceder</a></div>
                <div class="job-blur">
                    <h3>Pintura de fachada</h3>
                    <p>Se requiere personal para pintar exterior de casa habitación en zona norte...</p>
                    <span style="color:var(--primary); font-weight:900;">$3,200 MXN</span>
                </div>
            </div>
            <div class="job-card">
                <div class="job-overlay"><i class="fas fa-lock" style="color:var(--primary); font-size:2rem; margin-bottom:15px;"></i><a href="login.php" class="btn-unlock">Acceder</a></div>
                <div class="job-blur">
                    <h3>Reparación de Fuga</h3>
                    <p>Urgente: Tubería principal rota en jardín, se requiere plomero con equipo...</p>
                    <span style="color:var(--primary); font-weight:900;">$1,100 MXN</span>
                </div>
            </div>
            <div class="job-card">
                <div class="job-overlay"><i class="fas fa-lock" style="color:var(--primary); font-size:2rem; margin-bottom:15px;"></i><a href="login.php" class="btn-unlock">Acceder</a></div>
                <div class="job-blur">
                    <h3>Corto circuito sala</h3>
                    <p>Revisión de pastillas y cableado en planta baja por variaciones de voltaje...</p>
                    <span style="color:var(--primary); font-weight:900;">$950 MXN</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="nosotros" style="background: #080808;">
        <div style="text-align:center; max-width:900px; margin:0 auto;">
            <h2 class="section-title">Sobre <span>Chambitas</span></h2>
<p style="font-size:1.1rem; color:var(--text-gray); line-height:1.6;">
    Chambitas es una plataforma pensada para conectar personas dentro de una misma comunidad. 
    Aquí puedes publicar un trabajo, encontrar oportunidades o contactar directamente con alguien que pueda ayudarte. 
    Nuestro objetivo es hacer más fácil encontrar apoyo o generar ingresos de manera rápida y sencilla.
</p>
        </div>
        <div class="about-grid">
            <div class="about-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Seguridad</h3>
                <p>Usuarios verificados y tratos transparentes en todo momento.</p>
            </div>
            <div class="about-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Rapidez</h3>
                <p>Encuentra al experto que necesitas en menos de 10 minutos.</p>
            </div>
            <div class="about-card">
                <i class="fas fa-star"></i>
                <h3>Confianza</h3>
                <p>Basado en un sistema de reputación real y honesto.</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <img src="img/logo.png" alt="Logo" class="logo-img-footer">
                <p style="color: var(--text-gray); font-size:0.9rem;">El futuro del trabajo independiente está aquí. Únete a la red más grande de servicios.</p>
            </div>
            <div class="footer-links">
                <h4 style="margin-bottom:15px;">Legal</h4>
                <ul style="list-style:none; padding:0; color:var(--text-gray); font-size:0.9rem;">
                    <li style="margin-bottom:8px;"><a href="#" style="color:inherit; text-decoration:none;">Privacidad</a></li>
                    <li><a href="#" style="color:inherit; text-decoration:none;">Términos</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4 style="margin-bottom:15px;">Soporte</h4>
                <ul style="list-style:none; padding:0; color:var(--text-gray); font-size:0.9rem;">
                    <li style="margin-bottom:8px;"><a href="#" style="color:inherit; text-decoration:none;">Ayuda</a></li>
                    <li><a href="#" style="color:inherit; text-decoration:none;">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align:center; padding-top:40px; border-top:1px solid var(--border); margin-top:50px; color:#444;">
            &copy; 2026 Chambitas Corp. Master Admin Dashboard.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Inicializar Swiper
        var swiper = new Swiper(".mySwiper", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: { rotate: 0, stretch: 0, depth: 200, modifier: 1, slideShadows: true },
            loop: true,
            autoplay: { delay: 3000 }
        });

        // Menu hamburguesa
        const hamburger = document.querySelector(".hamburger");
        const navLinks = document.querySelector(".nav-links");
        
        hamburger.addEventListener("click", () => {
            navLinks.classList.toggle("active");
        });

        // Cerrar menú al hacer clic en un enlace
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
    </script>
</body>
</html>