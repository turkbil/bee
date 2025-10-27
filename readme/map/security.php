<?php
$page_title = "Güvenlik Sistemleri - Türk Bilişim Enterprise CMS";
$page_subtitle = "Enterprise Security Framework";
$page_badge = "🛡️ Güvenlik";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'authentication' => 'Kimlik Doğrulama',
    'authorization' => 'Yetkilendirme',
    'encryption' => 'Şifreleme',
    'threats' => 'Tehdit Koruması',
    'compliance' => 'Uyumluluk',
    'monitoring' => 'Güvenlik İzleme'
];

include 'header.php';
?>

<!-- Hero Section -->
<section id="hero" class="hero-section" style="position: relative; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%); background-size: 400% 400%; animation: gradientMove 8s ease infinite; overflow: hidden;">
    <!-- Animated Background -->
    <div class="hero-ai-visual" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(100, 181, 246, 0.1) 0%, rgba(79, 172, 254, 0.1) 25%, rgba(58, 123, 213, 0.1) 50%, rgba(26, 35, 126, 0.1) 100%); background-size: 400% 400%; animation: gradientMove 12s ease infinite reverse;"></div>
    
    <!-- Floating Particles -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none;">
        <div class="ai-particle" style="position: absolute; width: 4px; height: 4px; background: rgba(100, 181, 246, 0.6); border-radius: 50%; top: 20%; left: 10%; animation: float-ai 15s linear infinite;"></div>
        <div class="ai-particle" style="position: absolute; width: 6px; height: 6px; background: rgba(79, 172, 254, 0.4); border-radius: 50%; top: 60%; left: 80%; animation: float-ai 20s linear infinite 5s;"></div>
        <div class="ai-particle" style="position: absolute; width: 3px; height: 3px; background: rgba(58, 123, 213, 0.7); border-radius: 50%; top: 40%; left: 70%; animation: float-ai 18s linear infinite 2s;"></div>
        <div class="ai-particle" style="position: absolute; width: 5px; height: 5px; background: rgba(100, 181, 246, 0.5); border-radius: 50%; top: 80%; left: 20%; animation: float-ai 22s linear infinite 8s;"></div>
    </div>
    
    <!-- Subtle Glow Effect -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 800px; height: 400px; background: radial-gradient(circle, rgba(100, 181, 246, 0.1) 0%, transparent 70%); border-radius: 50%; filter: blur(40px);"></div>
    
    <!-- Hero Content -->
    <div style="position: relative; z-index: 10; max-width: 1100px; margin: 0 auto; padding: 0 2rem;">
        <!-- Main Hero Text -->
        <div style="text-align: center; margin-bottom: 4rem;">
            <div style="display: inline-block; margin-bottom: 2rem; padding: 0.5rem 1.5rem; background: rgba(100, 181, 246, 0.1); border: 1px solid rgba(100, 181, 246, 0.2); border-radius: 30px; backdrop-filter: blur(10px);">
                <span style="color: #64b5f6; font-size: 0.9rem; font-weight: 600;"><?php echo $page_badge; ?></span>
            </div>
            <h1 style="margin-bottom: 2rem; color: white; font-size: 3.5rem; line-height: 1.2; font-weight: 300; letter-spacing: -0.02em; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
                Enterprise-Grade<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Güvenlik Sistemi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Çok katmanlı güvenlik mimarisi ile<br>
                <span style="color: #64b5f6; font-weight: 600;">enterprise data protection</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Güvenlik Sistemleri Genel Bakış</h2>
    <p class="section-subtitle">
        Modern cybersecurity threats <span class="text-muted">(siber güvenlik tehditleri)</span> karşısında 
        enterprise-grade protection <span class="text-muted">(kurumsal seviye koruma)</span> sağlayan 
        comprehensive security framework <span class="text-muted">(kapsamlı güvenlik çerçevesi)</span>. 
        Multi-layered defense architecture <span class="text-muted">(çok katmanlı savunma mimarisi)</span> 
        ile zero-trust security model <span class="text-muted">(sıfır güven güvenlik modeli)</span> 
        implementation'ı. Advanced threat detection <span class="text-muted">(gelişmiş tehdit tespiti)</span>, 
        real-time monitoring <span class="text-muted">(gerçek zamanlı izleme)</span> ve automated response 
        <span class="text-muted">(otomatik yanıt)</span> capabilities ile proactive security posture sağlanır.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Multi-Layered Defense <span class="text-sm text-muted">(çok katmanlı savunma)</span></h3>
            <p class="text-secondary mb-4">
                Defense in depth strategy <span class="text-muted">(derinlemesine savunma stratejisi)</span> 
                ile multiple security layers implementation. Network security 
                <span class="text-muted">(ağ güvenliği)</span>, application security 
                <span class="text-muted">(uygulama güvenliği)</span>, data security 
                <span class="text-muted">(veri güvenliği)</span> ve endpoint protection 
                <span class="text-muted">(uç nokta koruması)</span> ile comprehensive coverage sağlanır. 
                Her katman independent security controls <span class="text-muted">(bağımsız güvenlik kontrolleri)</span> 
                içerir ve overlapping protection <span class="text-muted">(örtüşen koruma)</span> sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Network Security Layer</span> <span class="text-muted">(ağ güvenlik katmanı)</span><br><span class="text-sm text-secondary">→ Firewall, DDoS protection, intrusion detection systems</span></li>
                        <li>• <span class="tech-highlight">Application Security Layer</span> <span class="text-muted">(uygulama güvenlik katmanı)</span><br><span class="text-sm text-secondary">→ WAF, input validation, secure coding practices</span></li>
                        <li>• <span class="tech-highlight">Data Security Layer</span> <span class="text-muted">(veri güvenlik katmanı)</span><br><span class="text-sm text-secondary">→ Encryption, access controls, data loss prevention</span></li>
                        <li>• <span class="tech-highlight">Identity Security Layer</span> <span class="text-muted">(kimlik güvenlik katmanı)</span><br><span class="text-sm text-secondary">→ Multi-factor auth, privileged access management</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="eye"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Advanced Threat Detection <span class="text-sm text-muted">(gelişmiş tehdit tespiti)</span></h3>
            <p class="text-secondary mb-4">
                Machine learning algorithms <span class="text-muted">(makine öğrenmesi algoritmaları)</span> 
                ile behavioral analysis <span class="text-muted">(davranışsal analiz)</span> ve anomaly detection 
                <span class="text-muted">(anomali tespiti)</span>. Real-time threat intelligence 
                <span class="text-muted">(gerçek zamanlı tehdit istihbaratı)</span> ile emerging threats 
                <span class="text-muted">(gelişen tehditler)</span> karşısında proactive protection. 
                SIEM integration <span class="text-muted">(güvenlik bilgi ve olay yönetimi entegrasyonu)</span> 
                ile comprehensive security event correlation <span class="text-muted">(kapsamlı güvenlik olay korelasyonu)</span>.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Behavioral Analytics</span> <span class="text-muted">(davranışsal analitik)</span><br><span class="text-sm text-secondary">→ User behavior analysis, baseline establishment</span></li>
                        <li>• <span class="tech-highlight">Anomaly Detection</span> <span class="text-muted">(anomali tespiti)</span><br><span class="text-sm text-secondary">→ Statistical analysis, machine learning models</span></li>
                        <li>• <span class="tech-highlight">Threat Intelligence</span> <span class="text-muted">(tehdit istihbaratı)</span><br><span class="text-sm text-secondary">→ IOC feeds, threat hunting, attribution analysis</span></li>
                        <li>• <span class="tech-highlight">SIEM Integration</span> <span class="text-muted">(SIEM entegrasyonu)</span><br><span class="text-sm text-secondary">→ Log correlation, incident response automation</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="lock"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Zero-Trust Architecture <span class="text-sm text-muted">(sıfır güven mimarisi)</span></h3>
            <p class="text-secondary mb-4">
                "Never trust, always verify" principles <span class="text-muted">("asla güvenme, her zaman doğrula" prensipleri)</span> 
                ile modern security approach. Continuous authentication 
                <span class="text-muted">(sürekli kimlik doğrulama)</span>, micro-segmentation 
                <span class="text-muted">(mikro bölümleme)</span> ve least privilege access 
                <span class="text-muted">(en az ayrıcalık erişimi)</span> implementation. 
                Identity-centric security model <span class="text-muted">(kimlik merkezli güvenlik modeli)</span> 
                ile modern workplace security requirements'leri karşılanır.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Continuous Verification</span> <span class="text-muted">(sürekli doğrulama)</span><br><span class="text-sm text-secondary">→ Dynamic risk assessment, adaptive authentication</span></li>
                        <li>• <span class="tech-highlight">Micro-Segmentation</span> <span class="text-muted">(mikro bölümleme)</span><br><span class="text-sm text-secondary">→ Network isolation, application-level controls</span></li>
                        <li>• <span class="tech-highlight">Least Privilege Access</span> <span class="text-muted">(en az ayrıcalık erişimi)</span><br><span class="text-sm text-secondary">→ Just-in-time access, privilege escalation controls</span></li>
                        <li>• <span class="tech-highlight">Identity-Centric Security</span> <span class="text-muted">(kimlik merkezli güvenlik)</span><br><span class="text-sm text-secondary">→ Identity governance, access certification</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="book-open"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Compliance & Governance <span class="text-sm text-muted">(uyumluluk ve yönetişim)</span></h3>
            <p class="text-secondary mb-4">
                International compliance standards <span class="text-muted">(uluslararası uyumluluk standartları)</span> 
                ile regulatory requirements fulfillment. GDPR, ISO 27001, SOC 2 
                compliance framework'leri ile comprehensive governance model. 
                Automated compliance monitoring <span class="text-muted">(otomatik uyumluluk izleme)</span>, 
                audit trail management <span class="text-muted">(denetim izi yönetimi)</span> ve 
                regulatory reporting automation <span class="text-muted">(düzenleyici raporlama otomasyonu)</span> 
                ile continuous compliance assurance sağlanır.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">GDPR Compliance</span> <span class="text-muted">(GDPR uyumluluğu)</span><br><span class="text-sm text-secondary">→ Data subject rights, privacy by design</span></li>
                        <li>• <span class="tech-highlight">ISO 27001 Framework</span> <span class="text-muted">(ISO 27001 çerçevesi)</span><br><span class="text-sm text-secondary">→ Information security management system</span></li>
                        <li>• <span class="tech-highlight">SOC 2 Controls</span> <span class="text-muted">(SOC 2 kontrolleri)</span><br><span class="text-sm text-secondary">→ Security, availability, processing integrity</span></li>
                        <li>• <span class="tech-highlight">Audit Trail Management</span> <span class="text-muted">(denetim izi yönetimi)</span><br><span class="text-sm text-secondary">→ Immutable logs, forensic analysis capabilities</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Authentication Systems -->
<section id="authentication" class="section">
    <h2 class="section-title text-center">Kimlik Doğrulama Sistemleri</h2>
    <p class="section-subtitle text-center">
        Multi-factor authentication ile robust identity verification ve access control
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="fingerprint" class="w-6 h-6"></i>
            </div>
            <h3>Multi-Factor Authentication (MFA)</h3>
            <p class="text-secondary mb-3">
                Multiple authentication factors ile enhanced security. 
                SMS, email, authenticator apps ve biometric authentication support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• SMS-based verification codes</li>
                    <li>• Time-based one-time passwords (TOTP)</li>
                    <li>• Email verification links</li>
                    <li>• Biometric authentication</li>
                    <li>• Hardware security key support</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="key" class="w-6 h-6"></i>
            </div>
            <h3>Single Sign-On (SSO)</h3>
            <p class="text-secondary mb-3">
                Enterprise SSO integration ile unified authentication experience. 
                SAML, OAuth 2.0 ve OpenID Connect protocol support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• SAML 2.0 protocol support</li>
                    <li>• OAuth 2.0 ve OpenID Connect</li>
                    <li>• Active Directory integration</li>
                    <li>• LDAP authentication</li>
                    <li>• Social login providers</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Adaptive Authentication</h3>
            <p class="text-secondary mb-3">
                Risk-based authentication ile dynamic security controls. 
                Device fingerprinting, geolocation ve behavioral analysis.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Risk-based authentication scoring</li>
                    <li>• Device fingerprinting technology</li>
                    <li>• Geolocation-based controls</li>
                    <li>• Behavioral biometrics</li>
                    <li>• Adaptive step-up authentication</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <h3>Session Management</h3>
            <p class="text-secondary mb-3">
                Secure session handling ile session hijacking prevention. 
                Token-based authentication ve automatic session expiration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JWT token-based sessions</li>
                    <li>• Automatic session expiration</li>
                    <li>• Session fixation prevention</li>
                    <li>• Concurrent session management</li>
                    <li>• Secure cookie configuration</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Authorization & Access Control -->
<section id="authorization" class="section">
    <h2 class="section-title text-center">Yetkilendirme ve Erişim Kontrolü</h2>
    <p class="section-subtitle text-center">
        Role-based access control ile granular permission management ve security policies
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h3>Role-Based Access Control (RBAC)</h3>
            <p class="text-secondary mb-3">
                Hierarchical role management ile structured permission assignment. 
                Dynamic role creation, inheritance ve delegation capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Hierarchical role structure</li>
                    <li>• Permission inheritance</li>
                    <li>• Dynamic role assignment</li>
                    <li>• Role delegation mechanisms</li>
                    <li>• Temporal role assignments</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <h3>Attribute-Based Access Control (ABAC)</h3>
            <p class="text-secondary mb-3">
                Policy-based access decisions ile fine-grained authorization. 
                Context-aware permissions ve dynamic policy evaluation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Policy-based authorization</li>
                    <li>• Attribute-driven decisions</li>
                    <li>• Context-aware permissions</li>
                    <li>• Dynamic policy evaluation</li>
                    <li>• External data integration</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Privileged Access Management</h3>
            <p class="text-secondary mb-3">
                Administrative privilege control ile elevated access management. 
                Just-in-time access, privilege escalation ve monitoring.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Just-in-time access provisioning</li>
                    <li>• Privilege escalation controls</li>
                    <li>• Administrative session monitoring</li>
                    <li>• Privileged account discovery</li>
                    <li>• Access certification workflows</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <h3>Access Monitoring & Analytics</h3>
            <p class="text-secondary mb-3">
                Real-time access monitoring ile unauthorized access detection. 
                Access pattern analysis ve compliance reporting.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time access monitoring</li>
                    <li>• Access pattern analytics</li>
                    <li>• Unauthorized access detection</li>
                    <li>• Compliance reporting automation</li>
                    <li>• Access review workflows</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Encryption & Data Protection -->
<section id="encryption" class="section">
    <h2 class="section-title text-center">Şifreleme ve Veri Koruması</h2>
    <p class="section-subtitle text-center">
        Enterprise-grade encryption ile comprehensive data protection at rest ve in transit
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h3>Data Encryption at Rest</h3>
            <p class="text-secondary mb-3">
                Database ve file system encryption ile stored data protection. 
                AES-256 encryption, key rotation ve transparent data encryption.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• AES-256 database encryption</li>
                    <li>• File system level encryption</li>
                    <li>• Transparent data encryption (TDE)</li>
                    <li>• Encryption key rotation</li>
                    <li>• Column-level encryption</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>Data Encryption in Transit</h3>
            <p class="text-secondary mb-3">
                TLS/SSL encryption ile network communication protection. 
                Certificate management, perfect forward secrecy ve protocol security.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• TLS 1.3 protocol support</li>
                    <li>• Perfect forward secrecy</li>
                    <li>• Certificate pinning</li>
                    <li>• HSTS implementation</li>
                    <li>• End-to-end encryption</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="key" class="w-6 h-6"></i>
            </div>
            <h3>Key Management System</h3>
            <p class="text-secondary mb-3">
                Centralized cryptographic key management ile secure key lifecycle. 
                Hardware security modules, key escrow ve compliance support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Hardware security module (HSM)</li>
                    <li>• Key lifecycle management</li>
                    <li>• Key escrow ve recovery</li>
                    <li>• Multi-tenant key isolation</li>
                    <li>• Cryptographic agility</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="eye-off" class="w-6 h-6"></i>
            </div>
            <h3>Data Loss Prevention (DLP)</h3>
            <p class="text-secondary mb-3">
                Sensitive data identification ile unauthorized data transfer prevention. 
                Content inspection, policy enforcement ve incident response.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Sensitive data classification</li>
                    <li>• Content inspection engines</li>
                    <li>• Policy-based data protection</li>
                    <li>• Data exfiltration prevention</li>
                    <li>• Incident response automation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Threat Protection -->
<section id="threats" class="section">
    <h2 class="section-title text-center">Tehdit Koruması</h2>
    <p class="section-subtitle text-center">
        Advanced threat protection ile cyber attack prevention ve incident response
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <h3>Web Application Firewall (WAF)</h3>
            <p class="text-secondary mb-3">
                Application-layer protection ile OWASP Top 10 threat mitigation. 
                Real-time traffic analysis, bot protection ve rate limiting.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• OWASP Top 10 protection</li>
                    <li>• SQL injection prevention</li>
                    <li>• Cross-site scripting (XSS) filtering</li>
                    <li>• Bot traffic detection</li>
                    <li>• Rate limiting ve DDoS protection</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bug" class="w-6 h-6"></i>
            </div>
            <h3>Malware Detection & Prevention</h3>
            <p class="text-secondary mb-3">
                Multi-engine malware scanning ile file upload protection. 
                Signature-based ve heuristic analysis, sandbox execution.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-engine malware scanning</li>
                    <li>• Heuristic analysis engines</li>
                    <li>• Sandbox file execution</li>
                    <li>• Real-time threat feed integration</li>
                    <li>• Quarantine ve remediation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <h3>Intrusion Detection System (IDS)</h3>
            <p class="text-secondary mb-3">
                Network ve host-based intrusion detection ile suspicious activity monitoring. 
                Signature matching, anomaly detection ve threat correlation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Network-based intrusion detection</li>
                    <li>• Host-based monitoring</li>
                    <li>• Signature-based detection</li>
                    <li>• Behavioral anomaly detection</li>
                    <li>• Threat intelligence correlation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Incident Response Automation</h3>
            <p class="text-secondary mb-3">
                Automated incident response ile rapid threat containment. 
                Playbook execution, threat isolation ve forensic data collection.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated incident classification</li>
                    <li>• Response playbook execution</li>
                    <li>• Threat containment automation</li>
                    <li>• Forensic evidence collection</li>
                    <li>• Stakeholder notification</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Compliance -->
<section id="compliance" class="section">
    <h2 class="section-title text-center">Uyumluluk ve Governance</h2>
    <p class="section-subtitle text-center">
        Regulatory compliance ile international standards adherence ve governance frameworks
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="book" class="w-6 h-6"></i>
            </div>
            <h3>GDPR Compliance Framework</h3>
            <p class="text-secondary mb-3">
                European data protection regulation compliance ile privacy by design. 
                Data subject rights, consent management ve breach notification.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Data subject rights automation</li>
                    <li>• Consent management platform</li>
                    <li>• Data processing records</li>
                    <li>• Breach notification automation</li>
                    <li>• Privacy impact assessments</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <h3>ISO 27001 Implementation</h3>
            <p class="text-secondary mb-3">
                Information security management system ile systematic security approach. 
                Risk assessment, control implementation ve continuous improvement.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Information security policies</li>
                    <li>• Risk assessment framework</li>
                    <li>• Security control implementation</li>
                    <li>• Management review processes</li>
                    <li>• Continuous improvement cycle</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="file-check" class="w-6 h-6"></i>
            </div>
            <h3>SOC 2 Type II Controls</h3>
            <p class="text-secondary mb-3">
                Service organization controls ile trust service criteria compliance. 
                Security, availability, processing integrity ve confidentiality.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Security control framework</li>
                    <li>• Availability monitoring</li>
                    <li>• Processing integrity controls</li>
                    <li>• Confidentiality protection</li>
                    <li>• Independent audit trails</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <h3>Audit & Forensics</h3>
            <p class="text-secondary mb-3">
                Comprehensive audit logging ile forensic investigation capabilities. 
                Immutable logs, chain of custody ve digital evidence preservation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Immutable audit log storage</li>
                    <li>• Digital forensics tools</li>
                    <li>• Chain of custody procedures</li>
                    <li>• Evidence preservation automation</li>
                    <li>• Compliance reporting dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Security Monitoring -->
<section id="monitoring" class="section">
    <h2 class="section-title text-center">Güvenlik İzleme ve Analitik</h2>
    <p class="section-subtitle text-center">
        24/7 security monitoring ile real-time threat detection ve response capabilities
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Security Operations Center (SOC)</h3>
            <p class="text-secondary mb-3">
                Centralized security monitoring ile 24/7 threat detection. 
                Security analyst dashboard, incident management ve threat hunting.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• 24/7 security monitoring</li>
                    <li>• Real-time threat detection</li>
                    <li>• Incident escalation procedures</li>
                    <li>• Threat hunting capabilities</li>
                    <li>• Security metrics dashboard</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bar-chart" class="w-6 h-6"></i>
            </div>
            <h3>Security Information & Event Management</h3>
            <p class="text-secondary mb-3">
                SIEM platform ile log correlation ve security event analysis. 
                Custom rules, machine learning ve automated response integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Log aggregation ve correlation</li>
                    <li>• Custom detection rules</li>
                    <li>• Machine learning analytics</li>
                    <li>• Automated response triggers</li>
                    <li>• Compliance reporting</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bell" class="w-6 h-6"></i>
            </div>
            <h3>Intelligent Alerting System</h3>
            <p class="text-secondary mb-3">
                Smart alert correlation ile false positive reduction. 
                Priority scoring, escalation workflows ve multi-channel notifications.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Intelligent alert correlation</li>
                    <li>• False positive reduction</li>
                    <li>• Priority-based escalation</li>
                    <li>• Multi-channel notifications</li>
                    <li>• Alert fatigue prevention</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="target" class="w-6 h-6"></i>
            </div>
            <h3>Threat Intelligence Platform</h3>
            <p class="text-secondary mb-3">
                External threat intelligence integration ile proactive defense. 
                IOC feeds, attribution analysis ve threat landscape monitoring.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Threat intelligence feeds</li>
                    <li>• IOC automated ingestion</li>
                    <li>• Attribution analysis</li>
                    <li>• Threat landscape monitoring</li>
                    <li>• Predictive threat modeling</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>