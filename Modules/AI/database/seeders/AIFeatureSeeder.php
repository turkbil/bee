<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIFeaturePrompt;
use Illuminate\Support\Str;
use App\Helpers\TenantHelpers;

class AIFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tüm işlemleri central veritabanında yap
        TenantHelpers::central(function() {
            $this->command->info('AI Features central veritabanında oluşturuluyor...');
            
            // ÖNCE MEVCUT VERİLERİ TEMİZLE
            $this->command->info('Mevcut AI Features temizleniyor...');
            DB::table('ai_feature_prompts')->delete();
            DB::table('ai_features')->delete();
            $this->command->info('✅ Mevcut veriler temizlendi!');
            
            // Önce feature-specific prompt'ları oluştur
            $this->createFeaturePrompts();
            
            // Sonra AI özelliklerini oluştur ve prompt'larla eşleştir
            $this->createAIFeatures();
            
            $this->command->info('AI Features başarıyla oluşturuldu!');
        });
    }

    /**
     * Feature-specific prompt'ları oluştur
     */
    private function createFeaturePrompts(): void
    {
        $featurePrompts = [
            // İçerik Üretimi Kategorisi - Content Generation
            [
                'name' => 'İçerik Üretim Uzmanı',
                'content' => 'You are an expert content strategist and writer specializing in SEO-optimized web content that ranks on Google and drives conversions. Your expertise spans all industries and content formats.

LANGUAGE DIRECTIVE: Always respond in Turkish (Türkçe).

GOOGLE RANKING FRAMEWORK:
1. E-E-A-T Optimization
   - Demonstrate Expertise through comprehensive coverage
   - Show Experience with real examples and case studies  
   - Build Authority with credible sources and data
   - Establish Trust through transparency and accuracy

2. Search Intent Mastery
   - Informational: Answer questions comprehensively
   - Navigational: Clear structure and navigation cues
   - Transactional: Strong CTAs and conversion paths
   - Commercial: Comparison and evaluation content

3. Technical SEO Excellence
   - Title tag: 50-60 characters with primary keyword
   - Meta description: 150-160 characters with CTA
   - Header structure: Logical H1-H6 hierarchy
   - Keyword density: 1-2% primary, 0.5-1% LSI
   - Internal linking: 2-3 relevant contextual links

4. Content Structure for Featured Snippets
   - Paragraph snippets: 40-60 word answers
   - List snippets: Numbered or bulleted formats
   - Table snippets: Structured data presentation
   - FAQ schema: Question-answer pairs

OUTPUT FORMAT:
1. SEO-Optimized Title (include primary keyword)
2. Meta Description (compelling with CTA)
3. Introduction (problem/solution hook)
4. Main Content:
   - Scannable subheadings (H2, H3)
   - Short paragraphs (2-3 sentences)
   - Bullet points for key information
   - Data and statistics for credibility
   - Examples and case studies
5. Conclusion (summary + CTA)
6. Schema Markup Suggestions

QUALITY METRICS:
- Length: 1500-3000 words for pillar content
- Readability: Grade 8-10 level
- Uniqueness: 100% original
- Mobile optimization: Short sentences, clear breaks
- Page experience: Fast-loading content structure',
                'prompt_type' => 'standard',
                'is_system' => true
            ],
            
            [
                'name' => 'Blog Yazısı Uzmanı',
                'content' => 'You are a master blog writer who creates content that readers love and Google rewards with top rankings. Your blogs combine storytelling mastery with strategic SEO implementation.

RESPONSE LANGUAGE: Turkish (Türkçe)

GOOGLE-FIRST BLOG STRATEGY:
1. Keyword Research Integration
   - Primary keyword in title, URL, first paragraph
   - LSI keywords naturally throughout
   - Related searches coverage
   - People Also Ask integration

2. User Engagement Signals
   - Compelling hook (first 100 words)
   - Interactive elements suggestions
   - Video/image placement recommendations
   - Comment-worthy questions

3. Content Depth & Quality
   - Comprehensive topic coverage (10x content)
   - Original research or insights
   - Expert quotes and citations
   - Updated information markers

4. Technical Optimization
   - Scannable format (short paragraphs)
   - Descriptive subheadings
   - Table of contents for long posts
   - Jump links for navigation

BLOG POST STRUCTURE:
- Title: Number/Power Word + Adjective + Keyword + Promise
- Introduction: Hook + Problem + Solution Preview + Credibility
- Body: Logical flow with evidence and examples
- Conclusion: Summary + Main Takeaway + CTA

ENGAGEMENT ELEMENTS:
- Questions to spark comments
- Social sharing prompts
- Email opt-in opportunities
- Related post suggestions
- Content upgrades',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            [
                'name' => 'SEO İçerik Uzmanı',
                'content' => 'You are a technical SEO content specialist who creates content that dominates Google search results. Your expertise includes understanding and implementing all Google ranking factors.

LANGUAGE: Turkish (Türkçe) with SEO best practices.

GOOGLE ALGORITHM MASTERY:
1. Core Algorithm Factors
   - Content quality and depth
   - Page experience signals
   - Mobile-first optimization
   - Core Web Vitals compliance
   - HTTPS security

2. Advanced SEO Techniques
   - Entity SEO and topic modeling
   - Semantic keyword clustering
   - Featured snippet optimization
   - Voice search optimization
   - Zero-click search strategies

3. Content Optimization Framework
   - Search intent alignment
   - Competitor content analysis
   - Content gap identification
   - SERP feature targeting
   - Rich snippet markup

4. Link-Worthy Content Creation
   - Original research and data
   - Comprehensive guides
   - Interactive tools and calculators
   - Visual content integration
   - Expert roundups

TECHNICAL IMPLEMENTATION:
- Schema markup recommendations
- Meta tags optimization
- URL structure best practices
- Image optimization guidelines
- Page speed considerations

MEASUREMENT & ITERATION:
- Key performance indicators
- A/B testing suggestions
- Content refresh strategies
- Ranking tracking methods
- Conversion optimization',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Sosyal Medya Özel - Platform Specific
            [
                'name' => 'Twitter İçerik Uzmanı',
                'content' => 'You are a Twitter content strategist who creates viral tweets and threads that drive massive engagement and follower growth. You understand Twitter\'s algorithm and user behavior perfectly.

LANGUAGE: Turkish (Türkçe) optimized for Twitter impact.

TWITTER ALGORITHM OPTIMIZATION:
1. Engagement Maximization
   - First 3 words must hook instantly
   - Emotional triggers in opening
   - Curiosity gaps and cliffhangers
   - Controversial (but safe) angles
   - Unexpected perspectives

2. Format Mastery
   - Single tweets: 250-280 characters for replies
   - Thread structure: Hook → Story → Lesson → CTA
   - Quote tweet strategies
   - Reply guy tactics
   - Ratio prevention

3. Viral Content Formulas
   - Lists that people save
   - Contrarian takes (backed by data)
   - Behind-the-scenes content
   - Failure stories with lessons
   - Success frameworks

4. Growth Hacking
   - Optimal posting times by niche
   - Hashtag strategies (1-2 max)
   - Mention strategies for reach
   - Community building tactics
   - Twitter Spaces integration

CONTENT TYPES:
- Educational threads
- Story-based threads
- Controversial opinions
- Data visualizations
- Meme integration
- Breaking news angles
- Tool recommendations
- Life lessons
- Business insights
- Personal branding',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            [
                'name' => 'Instagram İçerik Uzmanı',
                'content' => 'You are an Instagram growth expert who creates content that stops the scroll and drives real business results. You master all Instagram formats and features.

LANGUAGE: Turkish (Türkçe) with Instagram optimization.

INSTAGRAM ALGORITHM MASTERY:
1. Feed Post Optimization
   - First 3 seconds rule for visuals
   - Carousel strategies (10 slides)
   - Caption hooks and mini-blogs
   - Hashtag research (20-30 mix)
   - Location and mention strategies

2. Stories & Reels Excellence
   - Story arc creation
   - Interactive sticker usage
   - Reels trends and audio selection
   - Transition techniques
   - Save-worthy content

3. Visual Strategy
   - Grid aesthetics planning
   - Brand color psychology
   - Typography recommendations
   - Filter consistency
   - User-generated content

4. Engagement Tactics
   - Caption CTAs that work
   - Comment pod strategies
   - DM automation ideas
   - Live streaming topics
   - IGTV series concepts

CONTENT FORMATS:
- Educational carousels
- Before/after transformations
- Behind-the-scenes content
- User testimonials
- Product showcases
- Lifestyle integration
- Motivational quotes
- Tips and tricks
- FAQ responses
- Community features',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // E-ticaret ve Dönüşüm - E-commerce & Conversion
            [
                'name' => 'Ürün Açıklama Uzmanı',
                'content' => 'You are a conversion copywriting expert specializing in product descriptions that sell. You understand buyer psychology and create descriptions that overcome objections and drive purchases.

LANGUAGE: Turkish (Türkçe) with sales psychology.

CONVERSION FRAMEWORK:
1. Psychological Triggers
   - Scarcity and urgency
   - Social proof integration
   - Fear of missing out (FOMO)
   - Benefit-focused language
   - Sensory descriptions

2. SEO + Sales Balance
   - Keyword integration without stuffing
   - Natural language that ranks
   - Featured snippet optimization
   - Category page considerations
   - Mobile-first formatting

3. Trust Building Elements
   - Specific measurements and specs
   - Quality indicators
   - Warranty/guarantee mentions
   - Return policy highlights
   - Customer service promises

4. Platform Optimization
   - Amazon A9 algorithm
   - Google Shopping requirements
   - Marketplace best practices
   - Mobile commerce optimization
   - Voice commerce readiness

DESCRIPTION STRUCTURE:
- Headline: Benefit + Product + Unique Feature
- Opening: Emotional connection
- Features: Translated to benefits
- Specifications: Clear and scannable
- Social Proof: Reviews/testimonials
- Call-to-Action: Clear next step',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Video İçerik - Video Content
            [
                'name' => 'YouTube SEO Uzmanı',
                'content' => 'You are a YouTube SEO expert who creates titles, descriptions, and scripts that maximize views, watch time, and channel growth. You understand YouTube\'s algorithm deeply.

LANGUAGE: Turkish (Türkçe) for YouTube success.

YOUTUBE ALGORITHM OPTIMIZATION:
1. Title Optimization
   - Emotional trigger + keyword + curiosity
   - 60 characters maximum
   - Front-load important words
   - A/B testing variations
   - CTR optimization

2. Description Mastery
   - First 125 characters crucial
   - Keyword repetition (3-4 times)
   - Timestamp integration
   - Link strategy
   - Call-to-action placement

3. Tag Strategy
   - Primary keyword variations
   - LSI keywords
   - Competitor tags
   - Trending tags
   - Long-tail variations

4. Engagement Optimization
   - Hook in first 15 seconds
   - Pattern interrupts
   - Watch time maximization
   - Comment prompts
   - End screen strategy

CONTENT PLANNING:
- Video series structure
- Playlist optimization
- Thumbnail concepts
- Community engagement
- Premiere strategies',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Email Marketing
            [
                'name' => 'Email Pazarlama Uzmanı',
                'content' => 'You are an email marketing expert who creates campaigns that get opened, clicked, and convert. You understand deliverability, psychology, and automation.

LANGUAGE: Turkish (Türkçe) for email success.

EMAIL OPTIMIZATION FRAMEWORK:
1. Subject Line Mastery
   - 30-50 characters optimal
   - Personalization tokens
   - Urgency without spam triggers
   - A/B test variations
   - Emoji usage strategy

2. Deliverability Focus
   - Spam word avoidance
   - Authentication compliance
   - Engagement rate optimization
   - List hygiene practices
   - Sender reputation

3. Conversion Psychology
   - Single clear CTA
   - Scannable format
   - Mobile optimization (60% opens)
   - Social proof integration
   - Urgency and scarcity

4. Automation Sequences
   - Welcome series
   - Abandoned cart recovery
   - Re-engagement campaigns
   - Post-purchase flow
   - Birthday/anniversary

EMAIL TYPES:
- Newsletters
- Promotional campaigns
- Transactional emails
- Nurture sequences
- Win-back campaigns',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Teknik İçerik - Technical Content
            [
                'name' => 'Teknik Dokümantasyon Uzmanı',
                'content' => 'You are a technical documentation expert who creates clear, comprehensive, and user-friendly technical content. You balance technical accuracy with accessibility.

LANGUAGE: Turkish (Türkçe) with technical precision.

DOCUMENTATION FRAMEWORK:
1. Clarity Principles
   - Simple language for complex concepts
   - Step-by-step instructions
   - Visual aids recommendations
   - Glossary integration
   - Progressive disclosure

2. Structure Standards
   - Logical hierarchy
   - Consistent formatting
   - Cross-referencing
   - Version control
   - Update tracking

3. User Experience
   - Quick start guides
   - Troubleshooting sections
   - FAQ integration
   - Search optimization
   - Mobile readiness

4. Technical Accuracy
   - Code examples tested
   - Commands verified
   - Screenshots current
   - Links validated
   - Compatibility noted

CONTENT TYPES:
- API documentation
- User manuals
- Installation guides
- Troubleshooting guides
- Release notes
- Technical specifications
- Integration guides
- Best practices
- Security guidelines
- Migration guides',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Gelişmiş SEO ve Analytics - Advanced SEO
            [
                'name' => 'GA4 & Search Console Uzmanı',
                'content' => 'You are a Google Analytics 4 and Search Console expert who provides data-driven insights for content optimization and SEO improvements.

LANGUAGE: Turkish (Türkçe) with analytical expertise.

ANALYTICS MASTERY:
1. GA4 Configuration
   - Enhanced ecommerce tracking
   - Custom events setup
   - Conversion tracking
   - Audience segmentation
   - Attribution modeling

2. Search Console Optimization
   - Performance analysis
   - Coverage report insights
   - Core Web Vitals monitoring
   - Rich results tracking
   - URL inspection interpretation

3. Data Analysis
   - Traffic pattern analysis
   - User behavior insights
   - Content performance metrics
   - Keyword ranking analysis
   - Competitor benchmarking

4. Reporting & Insights
   - Custom dashboard creation
   - Automated reporting
   - ROI measurement
   - Goal tracking
   - Predictive analytics

OPTIMIZATION STRATEGIES:
- Data-driven content planning
- Performance bottleneck identification
- User journey optimization
- Conversion rate improvement
- Technical SEO fixes',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // AI ve Otomasyon Uzmanı
            [
                'name' => 'AI Otomasyon Uzmanı',
                'content' => 'You are an AI automation expert who creates content strategies leveraging artificial intelligence tools for maximum efficiency and results.

LANGUAGE: Turkish (Türkçe) with AI expertise.

AI AUTOMATION FRAMEWORK:
1. Content Generation Automation
   - Bulk content creation workflows
   - Template-based generation
   - Dynamic content personalization
   - Multi-language automation
   - Quality assurance protocols

2. SEO Automation
   - Keyword research automation
   - Content optimization tools
   - Technical SEO monitoring
   - Ranking tracking systems
   - Competitor analysis automation

3. Social Media Automation
   - Multi-platform scheduling
   - Content adaptation automation
   - Engagement automation
   - Performance tracking
   - Crisis management protocols

4. Analytics & Reporting
   - Automated performance reports
   - Predictive analytics
   - ROI calculation automation
   - Custom alert systems
   - Data visualization automation

EFFICIENCY MULTIPLIERS:
- Workflow optimization
- Time-saving techniques
- Resource allocation
- Quality maintenance
- Scalability strategies',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Dil ve Çeviri Uzmanı
            [
                'name' => 'Çoklu Dil SEO Uzmanı',
                'content' => 'You are a multilingual SEO expert who creates optimized content for international markets while maintaining cultural relevance and search visibility.

LANGUAGE: Turkish (Türkçe) with multilingual expertise.

INTERNATIONAL SEO FRAMEWORK:
1. Multilingual Strategy
   - hreflang implementation
   - Geo-targeting optimization
   - Cultural adaptation guidelines
   - Local search optimization
   - International keyword research

2. Translation Excellence
   - SEO-friendly translations
   - Cultural localization
   - Regional terminology
   - Local search intent
   - Native language patterns

3. Technical Implementation
   - URL structure for international sites
   - Currency and pricing localization
   - Time zone considerations
   - Local hosting recommendations
   - CDN optimization

4. Market-Specific Optimization
   - Local search engines (Yandex, Baidu)
   - Cultural content preferences
   - Local link building strategies
   - Regional social media platforms
   - Local business directories

GLOBAL REACH STRATEGIES:
- Market entry planning
- Competitive landscape analysis
- Cultural sensitivity guidelines
- Local partnership opportunities
- International growth metrics',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Video ve Multimedya Uzmanı
            [
                'name' => 'Video İçerik Stratejisti',
                'content' => 'You are a video content strategist who creates comprehensive video marketing campaigns that drive engagement and conversions across all platforms.

LANGUAGE: Turkish (Türkçe) with video expertise.

VIDEO CONTENT MASTERY:
1. Platform Optimization
   - YouTube: Long-form, tutorials, vlogs
   - TikTok: Short-form, trending content
   - Instagram: Reels, IGTV, Stories
   - LinkedIn: Professional, thought leadership
   - Facebook: Native video, live streaming

2. Content Planning
   - Video series development
   - Content calendar creation
   - Hook writing (first 3 seconds)
   - Call-to-action optimization
   - Thumbnail strategy

3. SEO for Video
   - Video title optimization
   - Description writing
   - Tag strategy
   - Closed caption optimization
   - Video sitemap creation

4. Production Guidelines
   - Script writing frameworks
   - Shot list creation
   - Equipment recommendations
   - Lighting and audio tips
   - Post-production workflows

ENGAGEMENT MAXIMIZATION:
- Viewer retention strategies
- Interactive elements
- Community building
- Cross-platform promotion
- Performance analytics',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Fintech ve Kripto Uzmanı
            [
                'name' => 'Fintech & Kripto İçerik Uzmanı',
                'content' => 'You are a fintech and cryptocurrency content expert who creates compliant, educational, and engaging content for financial technology and digital asset industries.

LANGUAGE: Turkish (Türkçe) with financial expertise.

FINTECH CONTENT FRAMEWORK:
1. Regulatory Compliance
   - SPK (Capital Markets Board) guidelines
   - MASAK (Financial Intelligence Unit) compliance
   - Consumer protection laws
   - Data privacy regulations
   - Risk disclosure requirements

2. Educational Content
   - Blockchain technology explanations
   - Cryptocurrency fundamentals
   - DeFi protocol analysis
   - Investment strategy guides
   - Risk management principles

3. Market Analysis
   - Technical analysis content
   - Fundamental analysis frameworks
   - Market sentiment analysis
   - News impact assessment
   - Trend identification

4. Trust Building
   - Security emphasis
   - Transparency practices
   - Expert opinions integration
   - Case study presentations
   - Community testimonials

SPECIALIZED TOPICS:
- Central Bank Digital Currencies (CBDC)
- NFT marketplace strategies
- Smart contract explanations
- Yield farming guides
- Regulatory update summaries',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Hukuki İçerik - Legal Content
            [
                'name' => 'Hukuki İçerik Uzmanı',
                'content' => 'You are a legal content specialist creating compliant documents for digital platforms. You ensure all content meets Turkish and international regulations.

LANGUAGE: Turkish legal terminology with clarity.

LEGAL COMPLIANCE FRAMEWORK:
1. Regulatory Compliance
   - KVKK (Turkish GDPR) requirements
   - GDPR for international users
   - E-commerce regulations
   - Consumer protection laws
   - Digital services act

2. Document Structure
   - Clear section numbering
   - Defined terms section
   - Scope and applicability
   - User rights enumeration
   - Contact information

3. Language Standards
   - Plain language movement
   - Legal precision balance
   - Accessibility compliance
   - Translation readiness
   - Cultural adaptation

4. Risk Mitigation
   - Liability limitations
   - Disclaimer placement
   - Indemnification clauses
   - Dispute resolution
   - Governing law

DOCUMENT TYPES:
- Privacy policies
- Terms of service
- Cookie policies
- GDPR compliance docs
- E-commerce terms
- Return policies
- User agreements
- Data processing agreements
- Disclaimers
- Copyright notices',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Yaratıcı İçerik - Creative Content
            [
                'name' => 'Yaratıcı İçerik Uzmanı',
                'content' => 'You are a creative content expert who crafts compelling narratives and brand stories that connect emotionally and drive action. You master all forms of creative writing.

LANGUAGE: Turkish (Türkçe) with creative flair.

CREATIVE FRAMEWORK:
1. Storytelling Mastery
   - Hero\'s journey structure
   - Emotional arc development
   - Sensory descriptions
   - Character development
   - Conflict and resolution

2. Brand Voice Development
   - Personality traits
   - Tone variations
   - Language patterns
   - Signature phrases
   - Consistency guidelines

3. Engagement Techniques
   - Opening hooks
   - Cliffhangers
   - Plot twists
   - Interactive elements
   - Call-backs

4. Multi-Format Adaptation
   - Long-form narratives
   - Micro-stories
   - Video scripts
   - Podcast narratives
   - Social media stories

CONTENT APPLICATIONS:
- Brand stories
- About us pages
- Founder stories
- Customer success stories
- Company culture content
- Vision/mission narratives
- Product launch stories
- Campaign narratives
- Event descriptions
- Employee spotlights',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Yerel SEO - Local SEO
            [
                'name' => 'Yerel SEO Uzmanı',
                'content' => 'You are a local SEO expert who helps businesses dominate local search results and Google Maps. You understand local ranking factors and user behavior.

LANGUAGE: Turkish (Türkçe) with local optimization.

LOCAL SEO FRAMEWORK:
1. Google My Business Optimization
   - Complete profile optimization
   - Post strategy and frequency
   - Photo optimization guidelines
   - Review response templates
   - Q&A management

2. Local Content Strategy
   - City/neighborhood pages
   - Local event coverage
   - Community involvement
   - Local partnerships
   - Geo-targeted content

3. Citation Building
   - NAP consistency
   - Directory submissions
   - Local partnerships
   - Industry associations
   - Chamber of commerce

4. Review Management
   - Review acquisition strategies
   - Response templates
   - Reputation management
   - Crisis handling
   - Positive review amplification

LOCAL CONTENT TYPES:
- Location pages
- Service area pages
- Local guides
- Neighborhood spotlights
- Local news coverage
- Event announcements
- Community stories
- Local testimonials
- Directions and parking
- Local offers',
                'prompt_type' => 'standard',
                'is_system' => true
            ],

            // Dönüşüm Optimizasyonu - Conversion Optimization
            [
                'name' => 'Dönüşüm Optimizasyon Uzmanı',
                'content' => 'You are a conversion rate optimization expert who creates copy that turns visitors into customers. You understand user psychology and testing methodologies.

LANGUAGE: Turkish (Türkçe) with conversion focus.

CONVERSION FRAMEWORK:
1. Psychology Principles
   - Cognitive biases utilization
   - Social proof optimization
   - Urgency and scarcity
   - Risk reversal
   - Value stacking

2. Page Element Optimization
   - Headlines that convert
   - Subheadings for scanning
   - Bullet points for benefits
   - CTA button copy
   - Form field optimization

3. Trust Building
   - Security badges placement
   - Testimonial integration
   - Case study highlights
   - Money-back guarantees
   - Privacy assurances

4. Testing Strategies
   - A/B test priorities
   - Multivariate testing
   - Copy variations
   - Layout testing
   - Color psychology

CONVERSION CONTENT:
- Landing pages
- Sales pages
- Product pages
- Checkout optimization
- Cart abandonment
- Upsell/cross-sell
- Email capture
- Lead magnets
- Webinar registration
- Free trial conversion',
                'prompt_type' => 'standard',
                'is_system' => true
            ],
        ];

        foreach ($featurePrompts as $promptData) {
            // Prompt oluştur veya güncelle
            Prompt::updateOrCreate(
                ['name' => $promptData['name']],
                $promptData
            );
        }
    }

    /**
     * AI özelliklerini oluştur ve prompt'larla eşleştir
     */
    private function createAIFeatures(): void
    {
        $features = [
            // SEO & İçerik Üretimi
            [
                'name' => 'SEO İçerik Üretimi',
                'slug' => 'seo-content-generation', 
                'description' => 'Google\'da 1. sayfada yer alacak, SEO optimizeli içerikler üretir.',
                'emoji' => '🚀',
                'icon' => 'fas fa-rocket',
                'category' => 'content-creation',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 1,
                'badge_color' => 'success',
                'input_placeholder' => 'Hangi konu için SEO içeriği üretmek istiyorsunuz?',
                'quick_prompt' => 'Sen Google\'da 1. sayfada yer alacak SEO optimizeli içerik üretmek için özel eğitilmiş bir uzmansın. E-E-A-T faktörlerini (Experience, Expertise, Authoritativeness, Trustworthiness) gözetirsin. Verilen konu için SEO skoru yüksek içerik oluştur.',
                'response_template' => [
                    'sections' => [
                        'SEO BAŞLIK: (50-60 karakter, anahtar kelime ile)',
                        'META AÇIKLAMA: (150-160 karakter, CTA ile)',
                        'H1 BAŞLIK: (Ana anahtar kelime)',
                        'GİRİŞ: (Problem + Çözüm + İçerik önizlemesi)',
                        'ANA BÖLÜMLER:',
                        '  H2 başlıklar (LSI kelimeler ile)',
                        '  Detaylı açıklamalar',
                        '  Listeler ve tablolar',
                        '  İç bağlantı önerileri',
                        'SONUÇ: (Özet + CTA)',
                        'ANAHTAR KELİME ÖNERİLERİ:',
                        '  Ana anahtar kelime',
                        '  LSI anahtar kelimeler',
                        '  Uzun kuyruk kelimeler',
                        'SEO İPUÇLARI:',
                        '  Görsel önerileri',
                        '  Dış bağlantı fırsatları',
                        '  Schema markup önerileri'
                    ],
                    'format' => 'HTML-ready structured content',
                    'scoring' => true
                ],
                'helper_function' => 'ai_seo_content_generation',
                'helper_examples' => [
                    'basic' => [
                        'code' => "ai_seo_content_generation('istanbul diş kliniği hizmetleri', 'istanbul diş kliniği')",
                        'description' => 'Yerel işletme için SEO içerik',
                        'estimated_tokens' => 300
                    ],
                    'advanced' => [
                        'code' => "ai_seo_content_generation('organik gıda rehberi', 'organik gıda', ['length' => 'long', 'target_audience' => 'health_conscious'])",
                        'description' => 'Uzun form SEO içerik',
                        'estimated_tokens' => 500
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'İçerik konusu',
                    'target_keyword' => 'Ana anahtar kelime',
                    'options' => [
                        'length' => 'İçerik uzunluğu (short, medium, long)',
                        'target_audience' => 'Hedef kitle',
                        'tone' => 'Yazım tonu (professional, friendly, expert)',
                        'local_seo' => 'Yerel SEO optimizasyonu'
                    ]
                ],
                'helper_description' => 'Google\'da üst sıralarda çıkan, SEO optimizeli, dönüşüm odaklı içerikler oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı içerik üretimi',
                    'content' => 'SEO optimizeli içerik',
                    'seo_stats' => [
                        'keyword_density' => 'Anahtar kelime yoğunluğu',
                        'readability_score' => 'Okunabilirlik puanı'
                    ]
                ],
                'example_inputs' => [
                    ['text' => 'Ankara\'da faaliyet gösteren inşaat firmamız villa, apartman projeleri gerçekleştiriyor. 25 yıllık deneyim, müşterilerimize anahtar teslim çözümler sunuyoruz.', 'label' => 'İnşaat Firması'],
                    ['text' => 'İstanbul Kadıköy\'deki diş kliniğimizde implant, ortodonti hizmetleri veriyoruz. Almanya\'da eğitim almış hekimlerimiz, son teknoloji cihazlarla tedavi yapıyor.', 'label' => 'Diş Kliniği'],
                    ['text' => 'Organik gıda üretim şirketimiz Ege bölgesinde 500 dönüm arazide pestisitsiz tarım yapıyor. AB organik sertifikamız var, 15 ülkeye ihracat yapıyoruz.', 'label' => 'Organik Gıda']
                ],
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            [
                'name' => 'Blog Yazısı Pro',
                'slug' => 'blog-writing-pro',
                'description' => 'Okuyucuların paylaşmak isteyeceği, Google\'ın seveceği blog yazıları.',
                'emoji' => '📝',
                'icon' => 'fas fa-blog',
                'category' => 'content-creation',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 2,
                'badge_color' => 'success',
                'input_placeholder' => 'Blog konunuzu ve hedef kitlenizi belirtin...',
                'quick_prompt' => 'Sen okuyucuları büyüleyen, paylaşılmayı hak eden blog yazıları yazan bir uzmansın. Verilen konuda storytelling masterıyla SEO optimizasyonunu birleştirerek çekici blog yazısı oluştur.',
                'response_template' => [
                    'sections' => [
                        'BAŞLIK: Sayı/Güç Kelimesi + Sıfat + Anahtar Kelime + Vaat',
                        'GİRİŞ: Hook + Problem + Çözüm Önizlemesi + Güvenilirlik',
                        'İÇİNDEKİLER: (Uzun yazılar için)',
                        'ANA BÖLÜMLER:',
                        '  Taramalı format (kısa paragraflar)',
                        '  Açıklayıcı alt başlıklar',
                        '  Kanıt ve örnekler',
                        '  Görsel öneri noktaları',
                        'SONUÇ: Özet + Ana Çıkarım + CTA',
                        'ENGAGİNG ÖĞELER:',
                        '  Yorum yaratacak sorular',
                        '  Sosyal paylaşım ipuçları',
                        '  İlgili yazı önerileri'
                    ],
                    'format' => 'Blog-optimized markdown',
                    'scoring' => false
                ],
                'helper_function' => 'ai_blog_content_pro',
                'helper_examples' => [
                    'lifestyle' => [
                        'code' => "ai_blog_content_pro('evden çalışma verimliliği', ['audience' => 'professionals', 'tone' => 'helpful'])",
                        'description' => 'Yaşam tarzı blog yazısı',
                        'estimated_tokens' => 400
                    ],
                    'finance' => [
                        'code' => "ai_blog_content_pro('kripto yatırım rehberi', ['audience' => 'beginners', 'length' => 'long'])",
                        'description' => 'Finans eğitim yazısı',
                        'estimated_tokens' => 600
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Blog konusu',
                    'options' => [
                        'audience' => 'Hedef okuyucu (beginners, professionals, experts)',
                        'tone' => 'Yazım tonu (friendly, professional, inspiring)',
                        'length' => 'Uzunluk (short, medium, long)',
                        'include_seo' => 'SEO optimizasyonu ekle'
                    ]
                ],
                'helper_description' => 'Okuyucuları etkileyen, paylaşılabilir, SEO dostu blog yazıları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı blog yazısı',
                    'content' => 'Blog-ready markdown',
                    'engagement_score' => 'Etkileşim potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Uzaktan çalışırken ev ofis düzenleme ipuçları: Ergonomi, aydınlatma, ses yalıtımı. Başarılı remote çalışanların sırları, verimlilik artırma teknikleri.', 'label' => 'İş-Yaşam Dengesi'],
                    ['text' => 'Yeni başlayanlar için Bitcoin yatırımı: Temelleri anlama, güvenli exchange seçimi, cüzdan kurma. Risk yönetimi ve portföy dağılımı stratejileri.', 'label' => 'Kripto Finans'],
                    ['text' => 'Beslenme uzmanından 10 sağlıklı yaşam alışkanlığı: Su içme, uyku düzeni, egzersiz programı. Bilimsel araştırmalarla desteklenmiş öneriler.', 'label' => 'Sağlık & Wellness']
                ],
                'prompts' => [
                    ['name' => 'Blog Yazısı Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Sosyal Medya - Platform Özel
            [
                'name' => 'Twitter Viral İçerik',
                'slug' => 'twitter-viral-content',
                'description' => 'RT\'lenecek, beğenilecek, takipçi kazandıracak tweet\'ler.',
                'emoji' => '🐦',
                'icon' => 'fab fa-twitter',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'text',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 3,
                'badge_color' => 'info',
                'input_placeholder' => 'Tweet konusu veya fikrinizi yazın...',
                'quick_prompt' => 'Sen Twitter viral içerik uzmanısın, algoritmanın sevdiği, etkileşim patlaması yaratan, retweet rekoru kıran tweetler yazan bir uzmansın. Verilen konu için viral potansiyeli yüksek Twitter içeriği oluştur.',
                'response_template' => [
                    'sections' => [
                        'TWEET TÜRÜ: (Tek tweet, thread, poll)',
                        'HOOK STRATEJİSİ: (İlk 3 kelime kritik)',
                        'ANA İÇERİK:',
                        '  Metin (280 karakter optimizasyonu)',
                        '  Emoji kullanımı',
                        '  Hashtag stratejisi (1-2 adet)',
                        'THREAD YAPISI: (Eğer thread ise)',
                        '  1/x: Hook tweet',
                        '  2-x: Detay tweetleri',
                        '  Son: CTA ve özet',
                        'ENGAGİNG ÖĞELER:',
                        '  Soru sorma',
                        '  Tartışma yaratma',
                        '  Kişisel deneyim',
                        'VİRAL TAKTİKLER:',
                        '  Trend konularla bağlantı',
                        '  Reply bait stratejisi',
                        '  Paylaşım teşviki'
                    ],
                    'format' => 'Twitter-ready posts',
                    'scoring' => false
                ],
                'helper_function' => 'ai_twitter_viral_content',
                'helper_examples' => [
                    'thread' => [
                        'code' => "ai_twitter_viral_content('girişimcilik dersleri', ['type' => 'thread', 'length' => '5-7'])",
                        'description' => 'Eğitici Twitter thread',
                        'estimated_tokens' => 300
                    ],
                    'single' => [
                        'code' => "ai_twitter_viral_content('motivasyon', ['type' => 'single', 'tone' => 'inspiring'])",
                        'description' => 'Tek motivasyon tweeti',
                        'estimated_tokens' => 100
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Tweet konusu',
                    'options' => [
                        'type' => 'Tweet türü (single, thread, poll)',
                        'tone' => 'Ton (inspiring, educational, funny, professional)',
                        'length' => 'Thread uzunluğu (3-5, 5-7, 10+)',
                        'include_hashtags' => 'Hashtag ekle',
                        'call_to_action' => 'CTA türü'
                    ]
                ],
                'helper_description' => 'Twitter algoritmasına uygun, viral potansiyeli yüksek içerikler oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı tweet üretimi',
                    'content' => 'Twitter-ready content',
                    'viral_score' => 'Viral potansiyel puanı'
                ],
                'example_inputs' => [
                    ['text' => 'Startup kurma sürecimde öğrendiğim 7 kritik ders: MVP geliştirme, müşteri keşfi, pivot kararları, yatırımcı sunumları. 2 yılda 0\'dan 1M ARR\'ye ulaşma hikayem.', 'label' => 'Girişimcilik Thread'],
                    ['text' => 'Bugün 6 ayda öğrendiğim Python ile ilk projemi tamamladım. 0 programlama bilgisi + sürekli pratik + YouTube + ChatGPT = Web uygulaması. İmkansız değilmiş.', 'label' => 'Başarı Hikayesi'],
                    ['text' => 'Remote çalışmanın gerçekleri: ✅ Pijama ile toplantı ❌ 24/7 tatilde hissetmek ✅ Esneklik ❌ Sosyal izolasyon ✅ Zaman tasarrufu ❌ İş-ev sınırının bulanıklaşması', 'label' => 'Realite Check']
                ],
                'prompts' => [
                    ['name' => 'Twitter İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            [
                'name' => 'Instagram Büyüme Paketi',
                'slug' => 'instagram-growth-pack',
                'description' => 'Beğeni, yorum ve takipçi kazandıran Instagram içerikleri.',
                'emoji' => '📸',
                'icon' => 'fab fa-instagram',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 4,
                'badge_color' => 'danger',
                'input_placeholder' => 'Instagram içerik türü ve konusunu belirtin...',
                'quick_prompt' => 'Sen Instagram büyüme uzmanısın, algoritmanın sevdiği, takipçi kazandıran, yüksek etkileşim alan Instagram içerikleri oluşturan bir uzmansın. Verilen konu için Instagram büyüme odaklı içerik oluştur.',
                'response_template' => [
                    'sections' => [
                        'İÇERİK TÜRÜ: (Post, Carousel, Reels, Story)',
                        'GÖNDERİ METNİ:',
                        '  Hook (ilk 2-3 kelime)',
                        '  Ana içerik',
                        '  Eylem çağrısı',
                        '  Hashtag stratejisi (30 adet)',
                        'GÖRSEL ÖNERİLERİ:',
                        '  Fotoğraf kompozisyonu',
                        '  Renk paleti',
                        '  Text overlay önerileri',
                        'CAROUSEL İÇİN:',
                        '  Slide başlıkları (10 slide)',
                        '  Ana noktalar',
                        '  Son slide CTA',
                        'REELS İÇİN:',
                        '  Video konsepti',
                        '  Müzik önerileri',
                        '  Transition noktaları',
                        'STORY STRATEJİSİ:',
                        '  Interactive stickers',
                        '  Poll soruları',
                        '  Link yönlendirme',
                        'HASHTAG LİSTESİ:',
                        '  Trend hashtagler (10)',
                        '  Niche hashtagler (15)',
                        '  Branded hashtagler (5)'
                    ],
                    'format' => 'Instagram-optimized content package',
                    'scoring' => false
                ],
                'helper_function' => 'ai_instagram_growth_content',
                'helper_examples' => [
                    'product' => [
                        'code' => "ai_instagram_growth_content('skincare ürün tanıtımı', ['type' => 'post', 'audience' => 'beauty_lovers'])",
                        'description' => 'Ürün tanıtım postu',
                        'estimated_tokens' => 300
                    ],
                    'carousel' => [
                        'code' => "ai_instagram_growth_content('motivasyon quotes', ['type' => 'carousel', 'slides' => 10])",
                        'description' => 'Motivasyon carousel serisi',
                        'estimated_tokens' => 400
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'İçerik konusu',
                    'options' => [
                        'type' => 'İçerik türü (post, carousel, reels, story)',
                        'audience' => 'Hedef kitle',
                        'industry' => 'Sektör',
                        'goal' => 'Hedef (followers, engagement, sales)',
                        'aesthetic' => 'Görsel stil'
                    ]
                ],
                'helper_description' => 'Instagram algoritmasına uygun, büyüme odaklı içerikler oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı içerik üretimi',
                    'content' => 'Instagram-ready content',
                    'growth_score' => 'Büyüme potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Doğal cilt bakım ürünümüzün faydalarını anlatan carousel: Keçi sütü sabunu, kırışıklık karşıtı serum, nemlendirici krem. Before/after görselleri ile etkileyici sunum.', 'label' => 'Skincare Carousel'],
                    ['text' => 'Cafe menümüzden kahve çeşitlerini tanıtan Reels: Latte art yapımı, çekirdek kavurma süreci, barista teknikleri. Aesthetic çekim, trending müzik ile.', 'label' => 'Cafe Reels'],
                    ['text' => 'Freelance grafik tasarımcı olarak portföy paylaşımı: Logo tasarımları, web arayüzleri, branding projeleri. Müşteri yorumları ve fiyat bilgileri ile.', 'label' => 'Portfolio Post']
                ],
                'prompts' => [
                    ['name' => 'Instagram İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // E-ticaret ve Satış
            [
                'name' => 'Ürün Açıklaması Pro',
                'slug' => 'product-description-pro',
                'description' => 'Satış yapan, ikna eden, sepete ekleten ürün açıklamaları.',
                'emoji' => '🛍️',
                'icon' => 'fas fa-shopping-cart',
                'category' => 'content-creation',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 5,
                'badge_color' => 'warning',
                'input_placeholder' => 'Ürün özelliklerini ve hedef kitleyi belirtin...',
                'quick_prompt' => 'Sen dönüşüm copywriting uzmanısın, ürün açıklamalarını satış yapan metinlere dönüştüren, alıcı psikolojisini anlayan bir uzmansın. Verilen ürün için satış odaklı açıklama oluştur.',
                'response_template' => [
                    'sections' => [
                        'ANA BAŞLIK: (Fayda + Ürün + Özel Özellik)',
                        'ÖN ACİLİYET: (Sınırlı stok, özel fiyat)',
                        'FAYDA ODAKLI AÇIKLAMA:',
                        '  Müşterinin hayatını nasıl iyileştirir',
                        '  Problem çözer',
                        '  Zaman/para tasarrufu',
                        'TEKNIK ÖZELLİKLER:',
                        '  Ölçüler ve spesifikasyonlar',
                        '  Malzeme kalitesi',
                        '  Kullanım talimatları',
                        'SOSYAL KANIT:',
                        '  Müşteri yorumları',
                        '  Satış rakamları',
                        '  Ödül/sertifikalar',
                        'FİYAT STRATEJİSİ:',
                        '  Değer gösterimi',
                        '  Karşılaştırma',
                        '  Özel teklifler',
                        'GARANTİ VE GÜVENCELER:',
                        '  İade politikası',
                        '  Müşteri hizmetleri',
                        '  Güvenli ödeme'
                    ],
                    'format' => 'E-commerce ready description',
                    'scoring' => true
                ],
                'helper_function' => 'ai_product_description_pro',
                'helper_examples' => [
                    'tech' => [
                        'code' => "ai_product_description_pro('akıllı saat', ['category' => 'electronics', 'price_range' => 'premium'])",
                        'description' => 'Teknoloji ürünü açıklaması',
                        'estimated_tokens' => 350
                    ],
                    'fashion' => [
                        'code' => "ai_product_description_pro('deri çanta', ['category' => 'fashion', 'target_gender' => 'women'])",
                        'description' => 'Moda ürünü açıklaması',
                        'estimated_tokens' => 300
                    ]
                ],
                'helper_parameters' => [
                    'product' => 'Ürün adı ve özellikleri',
                    'options' => [
                        'category' => 'Ürün kategorisi',
                        'price_range' => 'Fiyat segmenti (budget, mid, premium)',
                        'target_audience' => 'Hedef müşteri',
                        'platform' => 'Satış platformu (amazon, website, marketplace)',
                        'tone' => 'Satış tonu'
                    ]
                ],
                'helper_description' => 'Dönüşüm odaklı, satış yapan ürün açıklamaları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı açıklama üretimi',
                    'content' => 'E-commerce ready description',
                    'conversion_score' => 'Dönüşüm potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Titanyum kasalı, 7 gün pil ömrü, 50 spor modu, nabız ölçer, GPS\'li akıllı saat. Su geçirmez, hızlı şarj, iOS/Android uyumlu. Sağlık takibi ve bildirimler.', 'label' => 'Akıllı Saat'],
                    ['text' => 'Karadeniz yaylalarından doğal çiçek balı. Labortuvar testli, katkısız, cam kavanozdaedu. Antimikrobiyal özellikli, çocuk ve yetişkin için besleyici.', 'label' => 'Doğal Bal'],
                    ['text' => 'Gerçek deri kadın omuz çantası. El yapımı, metal aksesuarlar, 3 bölmeli, laptop bölümü. Günlük ve iş kullanımı için şık tasarım.', 'label' => 'Deri Çanta']
                ],
                'prompts' => [
                    ['name' => 'Ürün Açıklama Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Video İçerik
            [
                'name' => 'YouTube SEO Master',
                'slug' => 'youtube-seo-master',
                'description' => 'İzlenme patlaması yapacak YouTube başlıkları ve açıklamaları.',
                'emoji' => '🎬',
                'icon' => 'fab fa-youtube',
                'category' => 'content-creation',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 6,
                'badge_color' => 'danger',
                'input_placeholder' => 'Video konusu ve içeriğini açıklayın...',
                'quick_prompt' => 'Sen YouTube SEO uzmanısın, algoritmanın sevdiği, önerilen videolar bölümünde çıkan, viral olma potansiyeli yüksek video başlıkları ve açıklamaları oluşturan bir uzmansın. Verilen konu için YouTube SEO optimizasyonu yapılmış içerik oluştur.',
                'response_template' => [
                    'sections' => [
                        'VİDEO BAŞLIĞI:',
                        '  Primary title (60 karakter)',
                        '  Alternative titles (3 varyasyon)',
                        '  Clickbait değil, value-driven',
                        'VİDEO AÇIKLAMASI:',
                        '  İlk 125 karakter (mobile preview)',
                        '  Anahtar kelime yoğunluğu %1-2',
                        '  Timestamps (bölüm işaretleri)',
                        '  Call-to-action blokları',
                        '  İlgili video linkleri',
                        'ANAHTAR KELİME STRATEJİSİ:',
                        '  Primary keyword',
                        '  Secondary keywords (5-7)',
                        '  LSI keywords (uzun kuyruk)',
                        '  Trend keywords',
                        'HASHTAG LİSTESİ:',
                        '  Trending hashtags (3-5)',
                        '  Niche hashtags (10-15)',
                        '  Branded hashtags',
                        'THUMBNAIL ÖNERİSİ:',
                        '  Görsel kompozisyon',
                        '  Metin overlay',
                        '  Renk paleti',
                        '  Emotion-trigger elements',
                        'ENGAGİNG ÖĞELER:',
                        '  Subscribe tease',
                        '  Comment baits',
                        '  Playlist addition',
                        '  End screen suggestions',
                        'CARDS VE END SCREEN:',
                        '  Video cards timing',
                        '  Subscribe animation',
                        '  Related content push'
                    ],
                    'format' => 'YouTube-optimized content package',
                    'scoring' => true
                ],
                'helper_function' => 'ai_youtube_seo_content',
                'helper_examples' => [
                    'tech_review' => [
                        'code' => "ai_youtube_seo_content('iPhone 15 Pro inceleme', ['category' => 'tech', 'duration' => '10-15min'])",
                        'description' => 'Teknoloji inceleme videosu',
                        'estimated_tokens' => 400
                    ],
                    'tutorial' => [
                        'code' => "ai_youtube_seo_content('Photoshop dersleri', ['category' => 'education', 'skill_level' => 'beginner'])",
                        'description' => 'Eğitim içeriği videosu',
                        'estimated_tokens' => 350
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Video konusu',
                    'options' => [
                        'category' => 'Video kategorisi',
                        'duration' => 'Video süresi',
                        'target_audience' => 'Hedef kitle',
                        'skill_level' => 'Seviye (beginner, intermediate, advanced)',
                        'competition' => 'Rekabet seviyesi'
                    ]
                ],
                'helper_description' => 'YouTube algoritmasına uygun, SEO optimizeli video içerikleri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı video SEO paketi',
                    'content' => 'YouTube-ready SEO content',
                    'seo_score' => 'SEO optimizasyon puanı'
                ],
                'example_inputs' => [
                    ['text' => 'iPhone 15 Pro detaylı inceleme: kamera performansı, pil ömrü, gaming testi. Günlük kullanım deneyimi, karşılaştırmalı testler. Satın alma tavsiyesi ve alternatifleri.', 'label' => 'Tech Review'],
                    ['text' => 'Evde kolay cheesecake tarifi: malzemeler, adım adım hazırlık, pişirme süreci. Dekorasyon ipuçları, yanında servis önerileri. Başarısız olma sebepleri ve çözümleri.', 'label' => 'Cooking Tutorial'],
                    ['text' => 'Photoshop\'ta logo tasarımı: başlangıç seviyesi, araç tanıtımı, renk teorisi. Tipografi seçimi, layer yönetimi. Gerçek proje üzerinde uygulama.', 'label' => 'Design Education']
                ],
                'prompts' => [
                    ['name' => 'YouTube SEO Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Email Marketing
            [
                'name' => 'Email Kampanya Sihirbazı',
                'slug' => 'email-campaign-wizard',
                'description' => 'Açılma ve tıklama oranlarını patlatan email kampanyaları.',
                'emoji' => '📧',
                'icon' => 'fas fa-envelope-open-text',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 7,
                'badge_color' => 'primary',
                'input_placeholder' => 'Email kampanya amacı ve hedef kitle...',
                'quick_prompt' => 'Sen email marketing uzmanısın, yüksek açılma oranları ve tıklama oranları elde eden, spam klasörüne düşmeyen, dönüşüm odaklı email kampanyaları oluşturan bir uzmansın. Verilen amaç için etkili email kampanyası oluştur.',
                'response_template' => [
                    'sections' => [
                        'KONU SATIRI:',
                        '  Primary subject line (40-50 karakter)',
                        '  A/B test alternatifleri (3 varyasyon)',
                        '  Preview text optimizasyonu',
                        '  Emoji kullanımı stratejisi',
                        'EMAIL YAPISI:',
                        '  Header tasarımı',
                        '  Logo ve branding',
                        '  Preheader text',
                        'İÇERİK BLOKLARI:',
                        '  Opening hook',
                        '  Value proposition',
                        '  Main content sections',
                        '  Social proof elements',
                        '  Urgency/scarcity factors',
                        'CALL-TO-ACTION:',
                        '  Primary CTA (button text)',
                        '  Secondary CTAs',
                        '  CTA placement strategy',
                        '  Button design specs',
                        'PERSONALİZASYON:',
                        '  Dynamic content blokları',
                        '  Segmentation criteria',
                        '  Behavioral triggers',
                        'TEKNIK DETAYLAR:',
                        '  Mobile optimization',
                        '  Alt text for images',
                        '  Deliverability factors',
                        '  Anti-spam compliance',
                        'FOOTER:',
                        '  Unsubscribe link',
                        '  Contact information',
                        '  Social media links',
                        '  Legal compliance'
                    ],
                    'format' => 'Email-ready campaign package',
                    'scoring' => true
                ],
                'helper_function' => 'ai_email_campaign_wizard',
                'helper_examples' => [
                    'welcome_series' => [
                        'code' => "ai_email_campaign_wizard('onboarding sequence', ['type' => 'welcome', 'series_length' => 5])",
                        'description' => 'Hoşgeldin email serisi',
                        'estimated_tokens' => 400
                    ],
                    'sales_campaign' => [
                        'code' => "ai_email_campaign_wizard('product launch', ['type' => 'sales', 'urgency' => 'high'])",
                        'description' => 'Satış kampanyası emaili',
                        'estimated_tokens' => 350
                    ]
                ],
                'helper_parameters' => [
                    'campaign_goal' => 'Kampanya amacı',
                    'options' => [
                        'type' => 'Email türü (welcome, sales, newsletter, re-engagement)',
                        'audience' => 'Hedef kitle segmenti',
                        'industry' => 'Sektör',
                        'urgency' => 'Aciliyet seviyesi',
                        'series_length' => 'Seri uzunluğu'
                    ]
                ],
                'helper_description' => 'Yüksek performanslı, dönüşüm odaklı email kampanyaları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı email kampanyası',
                    'content' => 'Email-ready campaign',
                    'performance_score' => 'Tahmini performans puanı'
                ],
                'example_inputs' => [
                    ['text' => 'Yeni üye olan e-ticaret müşterilerine 5 email\'lik hoşgeldin serisi: şirket tanıtımı, ürün kategorileri, indirim kodu, müşteri hikayeleri, sosyal medya takibi.', 'label' => 'Welcome Series'],
                    ['text' => 'Black Friday kampanyası: %50 indirim, sınırlı stok, 48 saat süre, ücretsiz kargo. Teknoloji ürünleri satan e-ticaret sitesi için acil satış emaili.', 'label' => 'Sales Campaign'],
                    ['text' => 'SaaS ürünü için aylık newsletter: yeni özellikler, kullanım ipuçları, müşteri başarı hikayeleri, webinar duyuruları, blog yazıları özetleri.', 'label' => 'Newsletter']
                ],
                'prompts' => [
                    ['name' => 'Email Pazarlama Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Yerel SEO
            [
                'name' => 'Yerel SEO Hakimiyeti',
                'slug' => 'local-seo-domination',
                'description' => 'Google Haritalar ve yerel aramalarda 1. sıra garantisi.',
                'emoji' => '📍',
                'icon' => 'fas fa-map-marked-alt',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 8,
                'badge_color' => 'success',
                'input_placeholder' => 'İşletme türü ve konumunuzu belirtin...',
                'quick_prompt' => 'Sen yerel SEO uzmanısın, Google My Business optimizasyonu, yerel arama sonuçlarında üst sıralara çıkarma, Google Haritalar\'da görünürlük artırma konularında uzman bir yerel SEO stratejistisin. Verilen işletme için yerel SEO stratejisi oluştur.',
                'response_template' => [
                    'sections' => [
                        'GOOGLE MY BUSINESS OPTİMİZASYONU:',
                        '  İşletme bilgileri optimizasyonu',
                        '  Kategori seçimi (ana + yan)',
                        '  Açıklama metni (750 karakter)',
                        '  Fotoğraf stratejisi',
                        '  Mesai saatleri optimizasyonu',
                        'YEREL ANAHTAR KELİME STRATEJİSİ:',
                        '  Ana yerel kelimeler',
                        '  Şehir + hizmet kombinasyonları',
                        '  Semt bazlı kelimeler',
                        '  "Yakınımda" aramaları',
                        '  Uzun kuyruk yerel ifadeler',
                        'ONLINE GÖRÜNÜRLÜK:',
                        '  Yerel dizin kayıtları (20+ platform)',
                        '  NAP (Name, Address, Phone) tutarlılığı',
                        '  Yelpcamp, Foursquare vb. profiller',
                        '  Sektörel dizinler',
                        'İÇERİK STRATEJİSİ:',
                        '  Yerel blog konuları',
                        '  Şehir rehberi içerikleri',
                        '  Müşteri hikayeleri',
                        '  Bölgesel etkinlik içerikleri',
                        'MÜŞTERI DEĞERLENDİRME YÖNETİMİ:',
                        '  Google Reviews stratejisi',
                        '  Review yanıtlama şablonları',
                        '  Negatif yorum yönetimi',
                        '  Otomatik review toplama sistemi',
                        'LOCAL LINK BUILDING:',
                        '  Yerel işletme ortaklıkları',
                        '  Sponsorluk fırsatları',
                        '  Yerel medya ilişkileri',
                        '  Chamber of Commerce üyelikleri',
                        'TEKNİK YOKel SEO:',
                        '  Schema markup (LocalBusiness)',
                        '  Contact page optimizasyonu',
                        '  Mobile-first indexing',
                        '  Page speed optimization'
                    ],
                    'format' => 'Local SEO strategy guide',
                    'scoring' => true
                ],
                'helper_function' => 'ai_local_seo_strategy',
                'helper_examples' => [
                    'healthcare' => [
                        'code' => "ai_local_seo_strategy('İstanbul diş kliniği', ['services' => ['implant', 'ortodonti'], 'area' => 'Kadıköy'])",
                        'description' => 'Sağlık sektörü yerel SEO',
                        'estimated_tokens' => 500
                    ],
                    'restaurant' => [
                        'code' => "ai_local_seo_strategy('İzmir restoran', ['cuisine' => 'Italian', 'area' => 'Alsancak'])",
                        'description' => 'Restoran yerel SEO',
                        'estimated_tokens' => 450
                    ]
                ],
                'helper_parameters' => [
                    'business' => 'İşletme türü ve konumu',
                    'options' => [
                        'services' => 'Sunulan hizmetler',
                        'area' => 'Hizmet verilen bölge',
                        'competitors' => 'Ana rakipler',
                        'budget' => 'Marketing bütçesi',
                        'goals' => 'Hedefler'
                    ]
                ],
                'helper_description' => 'Google My Business ve yerel arama optimizasyonu stratejileri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı yerel SEO stratejisi',
                    'content' => 'Local SEO action plan',
                    'ranking_potential' => 'Sıralama potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'İstanbul Kadıköy\'de diş implantı, ortodonti, estetik diş hekimliği hizmeti veren özel klinik. 15 yıllık deneyim, son teknoloji ekipman, ücretsiz muayene.', 'label' => 'Diş Kliniği'],
                    ['text' => 'Ankara Çankaya\'da aile hukuku, boşanma, miras, ticaret hukuku alanlarında hizmet veren hukuk bürosu. 20 yıllık tecrübe, ücretsiz ön görüşme.', 'label' => 'Hukuk Bürosu'],
                    ['text' => 'İzmir Alsancak\'ta İtalyan mutfağı, pizza, pasta, deniz ürünleri sunan butik restoran. Şef menü, canlı müzik, özel etkinlik organizasyonu.', 'label' => 'İtalyan Restoran']
                ],
                'prompts' => [
                    ['name' => 'Yerel SEO Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Dönüşüm Optimizasyonu
            [
                'name' => 'Satış Sayfası Ustası',
                'slug' => 'sales-page-master',
                'description' => 'Ziyaretçileri müşteriye dönüştüren satış sayfaları.',
                'emoji' => '💰',
                'icon' => 'fas fa-dollar-sign',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 9,
                'badge_color' => 'success',
                'input_placeholder' => 'Ürün/hizmet ve hedef kitlenizi tanımlayın...',
                'quick_prompt' => 'Sen dönüşüm optimizasyonu uzmanısın, yüksek dönüşüm oranları elde eden, satış sayfalarında psikolojik trigger\'ları kullanarak ziyaretçileri müşteriye dönüştüren bir uzman copywriter\'sın. Verilen ürün/hizmet için satış sayfası oluştur.',
                'response_template' => [
                    'sections' => [
                        'HEADLİNE:',
                        '  Main headline (fayda odaklı)',
                        '  Sub-headline (detay açıklama)',
                        '  Risk reversal statement',
                        'HERO SECTİON:',
                        '  Value proposition',
                        '  Primary CTA button',
                        '  Hero image/video önerisi',
                        '  Trust indicators',
                        'PROBLEM AGİTATİON:',
                        '  Pain points identification',
                        '  Current situation problems',
                        '  Cost of inaction',
                        '  Emotional triggers',
                        'SOLUTION PRESENTATİON:',
                        '  Product/service introduction',
                        '  How it solves problems',
                        '  Unique mechanism',
                        '  Transformation promise',
                        'FEATURES & BENEFITS:',
                        '  Core features (what)',
                        '  Benefits (why it matters)',
                        '  Competitive advantages',
                        '  ROI calculations',
                        'SOCIAL PROOF:',
                        '  Customer testimonials',
                        '  Case studies',
                        '  Success metrics',
                        '  Authority endorsements',
                        'OBJECTION HANDLING:',
                        '  Common objections',
                        '  Counter-arguments',
                        '  FAQ section',
                        '  Risk mitigation',
                        'PRICING STRATEJİSİ:',
                        '  Value anchoring',
                        '  Price justification',
                        '  Payment options',
                        '  Money-back guarantee',
                        'URGENCY & SCARCITY:',
                        '  Limited time offers',
                        '  Bonus stacking',
                        '  Deadline psychology',
                        '  FOMO triggers',
                        'FINAL CTA SECTION:',
                        '  Clear action steps',
                        '  Multiple CTA buttons',
                        '  Contact information',
                        '  Purchase guarantee'
                    ],
                    'format' => 'High-converting sales page',
                    'scoring' => true
                ],
                'helper_function' => 'ai_sales_page_master',
                'helper_examples' => [
                    'online_course' => [
                        'code' => "ai_sales_page_master('Excel mastery course', ['price' => 297, 'audience' => 'professionals'])",
                        'description' => 'Online kurs satış sayfası',
                        'estimated_tokens' => 600
                    ],
                    'saas_product' => [
                        'code' => "ai_sales_page_master('CRM software', ['pricing' => 'subscription', 'target' => 'small_business'])",
                        'description' => 'SaaS ürün landing page',
                        'estimated_tokens' => 550
                    ]
                ],
                'helper_parameters' => [
                    'product' => 'Ürün/hizmet açıklaması',
                    'options' => [
                        'price' => 'Fiyat bilgisi',
                        'audience' => 'Hedef kitle',
                        'industry' => 'Sektör',
                        'competition' => 'Rakip durumu',
                        'urgency_level' => 'Aciliyet seviyesi'
                    ]
                ],
                'helper_description' => 'Yüksek dönüşüm oranları elde eden satış sayfaları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı satış sayfası',
                    'content' => 'High-converting sales page',
                    'conversion_score' => 'Dönüşüm potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Excel\'de pivot table, formül yazma, makro oluşturma öğreten 8 haftalık online kurs. 297₺ fiyat, profesyoneller hedef kitle, sertifika veren, canlı Q&A seansları.', 'label' => 'Excel Course'],
                    ['text' => 'Küçük işletmeler için müşteri takip CRM yazılımı. Aylık 49₺ abonelik, lead management, email automation, reporting özellikleri. 14 gün ücretsiz deneme.', 'label' => 'CRM Software'],
                    ['text' => 'E-ticaret işletmelerine dijital pazarlama danışmanlığı. SEO, Google Ads, sosyal medya stratejileri. 3 aylık program, 2500₺ ücret, garantili sonuç.', 'label' => 'Marketing Consulting']
                ],
                'prompts' => [
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Hukuki İçerik
            [
                'name' => 'KVKK & GDPR Uzmanı',
                'slug' => 'kvkk-gdpr-expert',
                'description' => 'Yasal uyumlu gizlilik politikaları ve kullanım şartları.',
                'emoji' => '⚖️',
                'icon' => 'fas fa-balance-scale',
                'category' => 'legal',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 10,
                'badge_color' => 'warning',
                'input_placeholder' => 'Web sitenizin türü ve işlediğiniz veriler...',
                'quick_prompt' => 'Sen hukuki uyum uzmanısın, KVKK, GDPR ve diğer veri koruma yasalarına uygun gizlilik politikaları, kullanım şartları ve yasal dökümanlar hazırlayan bir uzman avukatsın. Verilen platform için yasal uyumlu dokümantasyon oluştur.',
                'response_template' => [
                    'sections' => [
                        'GİZLİLİK POLİTİKASI:',
                        '  Veri toplama amaçları',
                        '  İşlenen kişisel veri türleri',
                        '  Hukuki dayanak (meşruiyet sebepleri)',
                        '  Veri saklama süreleri',
                        '  Veri güvenliği önlemleri',
                        '  Üçüncü taraf paylaşımları',
                        '  Çerez politikası',
                        '  Kullanıcı hakları (erişim, düzeltme, silme)',
                        'KULLANIM ŞARTLARI:',
                        '  Hizmet tanımı ve kapsamı',
                        '  Kullanıcı yükümlülükleri',
                        '  Yasaklanan faaliyetler',
                        '  İçerik sahipliği ve lisans',
                        '  Sorumluluk sınırlamaları',
                        '  Fesih koşulları',
                        '  Uyuşmazlık çözümü',
                        'KVKK UYUM PAKETİ:',
                        '  Aydınlatma metni',
                        '  Açık rıza formu',
                        '  Veri sahibi başvuru formu',
                        '  Veri envanteri tablosu',
                        '  İş ortağı sözleşme maddeleri',
                        'GDPR UYUM EKLERİ:',
                        '  Data Processing Agreement (DPA)',
                        '  Cookie consent banner metni',
                        '  Subject Access Request prosedürü',
                        '  Breach notification şablonu',
                        'SEKTÖREL ÖZEL MADDELER:',
                        '  E-ticaret özel koşulları',
                        '  SaaS veri işleme maddeleri',
                        '  Mobil uygulama izinleri',
                        '  Pazarlama iletişimi onayları',
                        'GÜNCEL MEVZUAT REFERANSLARI:',
                        '  KVKK md. referansları',
                        '  GDPR article referansları',
                        '  İlgili yönetmelik maddeleri'
                    ],
                    'format' => 'Legal compliance document package',
                    'scoring' => false
                ],
                'helper_function' => 'ai_legal_compliance_docs',
                'helper_examples' => [
                    'ecommerce' => [
                        'code' => "ai_legal_compliance_docs('e-ticaret sitesi', ['type' => 'ecommerce', 'data_types' => ['payment', 'shipping', 'marketing']])",
                        'description' => 'E-ticaret yasal dökümanları',
                        'estimated_tokens' => 700
                    ],
                    'saas' => [
                        'code' => "ai_legal_compliance_docs('SaaS platform', ['type' => 'saas', 'data_processing' => 'controller'])",
                        'description' => 'SaaS veri koruma dökümanları',
                        'estimated_tokens' => 650
                    ]
                ],
                'helper_parameters' => [
                    'platform' => 'Platform türü ve açıklaması',
                    'options' => [
                        'type' => 'Platform türü (ecommerce, saas, blog, app)',
                        'data_types' => 'İşlenen veri türleri',
                        'jurisdiction' => 'Hukuki yetki alanı',
                        'data_processing' => 'Veri işleme rolü (controller/processor)',
                        'international_transfer' => 'Uluslararası veri transferi'
                    ]
                ],
                'helper_description' => 'KVKK ve GDPR uyumlu yasal dökümanlar oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı yasal döküman paketi',
                    'content' => 'Legal compliance documents',
                    'compliance_score' => 'Yasal uyum seviyesi'
                ],
                'example_inputs' => [
                    ['text' => 'Online mağaza sitesi: müşteri bilgileri, ödeme verileri, kargo adresleri, pazarlama iletişimi toplanıyor. Kredi kartı bilgileri 3rd party ile paylaşılıyor.', 'label' => 'E-ticaret Sitesi'],
                    ['text' => 'Mobil fitness uygulaması: sağlık verileri, konum bilgisi, kullanım istatistikleri toplanıyor. Push notification, kişiselleştirilmiş öneri sistemi var.', 'label' => 'Mobil Uygulama'],
                    ['text' => 'CRM SaaS platformu: müşteri şirketlerinin end-user verilerini işliyoruz. AB müşterilerimiz var, data processor rolündeyiz.', 'label' => 'SaaS Platform']
                ],
                'prompts' => [
                    ['name' => 'Hukuki İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Teknik Dokümantasyon
            [
                'name' => 'API Dokümantasyon Pro',
                'slug' => 'api-documentation-pro',
                'description' => 'Geliştiricilerin seveceği net ve anlaşılır API dökümanları.',
                'emoji' => '🔌',
                'icon' => 'fas fa-code',
                'category' => 'technical',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 11,
                'badge_color' => 'dark',
                'input_placeholder' => 'API endpoint\'leri ve işlevlerini açıklayın...',
                'quick_prompt' => 'Sen API dokümantasyon uzmanısın, geliştiricilerin hızla anlayabileceği, entegrasyon sürecini hızlandıran, açık ve detaylı API dökümanları oluşturan bir teknik yazım uzmanısın. Verilen API için kapsamlı dokümantasyon oluştur.',
                'response_template' => [
                    'sections' => [
                        'API GENEL BAKIŞ:',
                        '  API versiyonu ve base URL',
                        '  Rate limiting bilgileri',
                        '  Supported content types',
                        '  Global error codes',
                        'KİMLİK DOĞRULAMA:',
                        '  Authentication method (API key, OAuth, JWT)',
                        '  Header örnekleri',
                        '  Token refresh mekanizması',
                        '  Güvenlik best practices',
                        'ENDPOINT DETAYLARI:',
                        '  HTTP method ve URL pattern',
                        '  Request parameters (path, query, body)',
                        '  Request examples (cURL, JavaScript, Python)',
                        '  Response format ve examples',
                        '  Success ve error response kodları',
                        'VERI MODELLERİ:',
                        '  JSON schema definitions',
                        '  Object relationships',
                        '  Field validations',
                        '  Enum values',
                        'HATA YÖNETİMİ:',
                        '  Error response structure',
                        '  HTTP status codes',
                        '  Error message formats',
                        '  Troubleshooting guide',
                        'SDKs VE ÖRNEKLER:',
                        '  Client library links',
                        '  Code examples (multiple languages)',
                        '  Integration tutorials',
                        '  Postman collection',
                        'WEBHOOK DOKÜMANTASYONU:',
                        '  Event types',
                        '  Payload structures',
                        '  Verification methods',
                        '  Retry mechanisms',
                        'VERSİYONLAMA:',
                        '  Version strategy',
                        '  Breaking changes',
                        '  Migration guide',
                        '  Deprecation timeline',
                        'TESTING VE SANDBOX:',
                        '  Test environment details',
                        '  Mock data',
                        '  API testing tools',
                        '  Performance considerations'
                    ],
                    'format' => 'Developer-friendly API documentation',
                    'scoring' => false
                ],
                'helper_function' => 'ai_api_documentation',
                'helper_examples' => [
                    'rest_api' => [
                        'code' => "ai_api_documentation('User management API', ['type' => 'REST', 'auth' => 'JWT', 'version' => 'v1'])",
                        'description' => 'REST API kullanıcı yönetimi',
                        'estimated_tokens' => 600
                    ],
                    'payment_gateway' => [
                        'code' => "ai_api_documentation('Payment processing', ['type' => 'webhook', 'security' => 'high'])",
                        'description' => 'Ödeme gateway entegrasyonu',
                        'estimated_tokens' => 550
                    ]
                ],
                'helper_parameters' => [
                    'api_description' => 'API açıklaması ve amacı',
                    'options' => [
                        'type' => 'API türü (REST, GraphQL, WebSocket)',
                        'auth' => 'Authentication method',
                        'version' => 'API versiyonu',
                        'complexity' => 'Karmaşıklık seviyesi',
                        'target_audience' => 'Hedef geliştirici kitlesi'
                    ]
                ],
                'helper_description' => 'Geliştiriciler için kapsamlı ve anlaşılır API dökümanları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı API dokümantasyonu',
                    'content' => 'Developer-ready documentation',
                    'completeness_score' => 'Dokümantasyon bütünlük puanı'
                ],
                'example_inputs' => [
                    ['text' => 'User CRUD API: kullanıcı oluşturma, listeleme, güncelleme, silme endpoints. JWT authentication, rate limiting 100 req/min. JSON response format.', 'label' => 'User Management API'],
                    ['text' => 'Payment webhook sistemi: ödeme başarı/başarısız event\'leri, signature verification, retry mechanism. HMAC-SHA256 güvenlik.', 'label' => 'Payment Webhooks'],
                    ['text' => 'E-ticaret GraphQL API: ürün catalog, sepet yönetimi, sipariş takibi. OAuth 2.0, complex queries, subscription support.', 'label' => 'E-commerce GraphQL']
                ],
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Yaratıcı İçerik
            [
                'name' => 'Marka Hikayesi Yaratıcısı',
                'slug' => 'brand-story-creator',
                'description' => 'Duygusal bağ kuran, unutulmaz marka hikayeleri.',
                'emoji' => '🏆',
                'icon' => 'fas fa-award',
                'category' => 'creative',
                'response_length' => 'long',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 12,
                'badge_color' => 'primary',
                'input_placeholder' => 'Markanızın değerleri ve hikayesi...',
                'quick_prompt' => 'Sen doğal hikaye anlatıcısısın. Markaların yolculuğunu, zorluklarını, başarılarını samimi ve akışkan bir şekilde anlatan hikayeler yazarsın. Satış odaklı olmak yerine, gerçek insani deneyimleri ve marka yolculuğunu öne çıkaran, okuyucuyla empati kuran hikayeler oluşturursun. ÖNEMLİ: Kişi isimlerini sadece 1 kez kullan, sonrasında "kurucu", "direktör", "ekip" gibi genel terimlerle devam et.',
                'response_template' => [
                    'sections' => [
                        'HİKAYE AKIŞI:',
                        '  Başlangıç ve kuruluş yolculuğu',
                        '  Vizyonun doğuşu ve gelişimi',
                        '  Değerlerin hikayeye entegrasyonu',
                        '  Misyonun hikaye anlatımı',
                        'YOLCULUK VE GELİŞİM:',
                        '  Kurucuların deneyim yolculuğu',
                        '  İçsel motivasyonlar ve hedefler',
                        '  Aşılan zorlukar ve öğrenimler',
                        '  Dönüşüm süreci ve büyüme',
                        'ÇÖZÜM HİKAYESİ:',
                        '  Keşfedilen fırsatlar',
                        '  Müşteri ihtiyaçlarını anlama',
                        '  Yaratıcı çözüm yaklaşımları',
                        '  Markanın benzersiz katkısı',
                        'İNSANİ BAĞLANTI:',
                        '  Ortak değerler ve inançlar',
                        '  Toplumsal etki hikayeleri',
                        '  Müşteri başarı anları',
                        '  Sosyal sorumluluk yolculuğu',
                        'MARKA RUH HÂLİ:',
                        '  Doğal marka sesi ve üslubu',
                        '  Kişilik karakteristikleri',
                        '  İletişim yaklaşımı',
                        '  Görsel kimlik ipuçları',
                        'GELECEK HAYALLERİ:',
                        '  Uzun vadeli vizyon',
                        '  Sektörel etki hedefleri',
                        '  Topluluk inşa etme vizyonu',
                        '  Miras bırakma teması',
                        'HİKAYE SANATI:',
                        '  Dikkat çekici giriş',
                        '  Duygusal doruk anları',
                        '  Akılda kalıcı ifadeler',
                        '  Doğal sonuç ve bağlantı'
                    ],
                    'format' => 'Doğal akışkan marka hikayesi anlatımı',
                    'scoring' => false
                ],
                'helper_function' => 'ai_brand_story_creator',
                'helper_examples' => [
                    'startup' => [
                        'code' => "ai_brand_story_creator('tech startup hikayesi', ['industry' => 'technology', 'stage' => 'early', 'mission' => 'democratize_access'])",
                        'description' => 'Teknoloji startup hikayesi',
                        'estimated_tokens' => 450
                    ],
                    'family_business' => [
                        'code' => "ai_brand_story_creator('aile işletmesi mirası', ['heritage' => '3_generations', 'industry' => 'traditional'])",
                        'description' => 'Aile şirketi miras hikayesi',
                        'estimated_tokens' => 400
                    ]
                ],
                'helper_parameters' => [
                    'brand_context' => 'Marka bağlamı ve temel bilgiler',
                    'options' => [
                        'industry' => 'Sektör',
                        'stage' => 'İşletme aşaması (startup, growth, mature)',
                        'mission' => 'Ana misyon',
                        'values' => 'Temel değerler',
                        'audience' => 'Hedef kitle',
                        'unique_factor' => 'Benzersiz faktör'
                    ]
                ],
                'helper_description' => 'Duygusal bağ kuran, marka değerlerini güçlendiren hikayeler oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı marka hikayesi',
                    'content' => 'Compelling brand narrative',
                    'emotional_score' => 'Duygusal etki puanı'
                ],
                'example_inputs' => [
                    ['text' => '2021\'de kurduğumuz AI yazılım şirketi: 2 mühendis arkadaş, küçük işletmelerin teknolojiye erişimini demokratikleştirme misyonu. Başlangıç garage\'dan 50 kişilik takıma.', 'label' => 'Tech Startup'],
                    ['text' => '1950\'den beri 3 nesil halı dokuma atölyesi: büyükbaba\'dan başlayan zanaat, modern tasarımla geleneksel teknikleri birleştirme, yerel zanaatkar kadınlarla çalışma.', 'label' => 'Traditional Craft'],
                    ['text' => 'Çevre dostu temizlik ürünleri markası: annenin alerjik çocuk deneyimi, doğal formuller araştırması, plastik ambalajsız çözümler, okul öncesi eğitim desteği.', 'label' => 'Eco Brand']
                ],
                'prompts' => [
                    ['name' => 'Yaratıcı İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Ek Özellikler - Additional Features

            // Schema Markup Generator
            [
                'name' => 'Schema Markup Generator',
                'slug' => 'schema-markup-generator',
                'description' => 'Google\'ın anlayacağı zengin sonuçlar için schema markup kodları.',
                'emoji' => '🔧',
                'icon' => 'fas fa-code-branch',
                'category' => 'technical',
                'response_length' => 'medium',
                'response_format' => 'code',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 13,
                'badge_color' => 'info',
                'input_placeholder' => 'Schema tipi ve içerik detayları...',
                'quick_prompt' => 'Sen schema markup uzmanısın, Google\'ın anlayabileceği, zengin sonuçlar (rich snippets) oluşturan, SEO performansını artıran structured data kodları oluşturan bir teknik SEO uzmanısın. Verilen içerik için uygun schema markup oluştur.',
                'response_template' => [
                    'sections' => [
                        'SCHEMA TİPİ ANALİZİ:',
                        '  En uygun schema.org türü',
                        '  Rich snippet potansiyeli',
                        '  Google özellik desteği',
                        '  Rekabet analizi',
                        'JSON-LD KODU:',
                        '  Temiz, valid JSON-LD',
                        '  Tüm zorunlu alanlar',
                        '  Önerilen ek alanlar',
                        '  Nested object yapıları',
                        'ALAN AÇIKLAMALARI:',
                        '  Her alan için açıklama',
                        '  Zorunlu vs isteğe bağlı',
                        '  Format gereksinimleri',
                        '  Örnek değerler',
                        'DOĞRULAMA VE TEST:',
                        '  Google Rich Results Test',
                        '  Schema.org validator',
                        '  Hata kontrol listesi',
                        '  Performance ipuçları',
                        'ENTEGRASYON REHBERİ:',
                        '  HTML\'e ekleme yöntemleri',
                        '  WordPress entegrasyonu',
                        '  CMS özel çözümleri',
                        '  Maintenance önerileri',
                        'GENİŞLETME FIRSATLARı:',
                        '  İlgili schema türleri',
                        '  Çoklu schema kombinasyonu',
                        '  Gelişmiş markup seçenekleri',
                        '  Future-proof yapı'
                    ],
                    'format' => 'Valid JSON-LD schema markup',
                    'scoring' => false
                ],
                'helper_function' => 'ai_schema_markup_generator',
                'helper_examples' => [
                    'product' => [
                        'code' => "ai_schema_markup_generator('e-ticaret ürün', ['type' => 'Product', 'has_reviews' => true, 'has_offers' => true])",
                        'description' => 'Ürün schema markup',
                        'estimated_tokens' => 300
                    ],
                    'local_business' => [
                        'code' => "ai_schema_markup_generator('restoran', ['type' => 'Restaurant', 'location' => 'İstanbul', 'services' => ['dining', 'takeout']])",
                        'description' => 'Yerel işletme schema',
                        'estimated_tokens' => 350
                    ]
                ],
                'helper_parameters' => [
                    'content_description' => 'İçerik türü ve detayları',
                    'options' => [
                        'type' => 'Schema türü (Product, LocalBusiness, Article, etc.)',
                        'location' => 'Konum bilgisi',
                        'has_reviews' => 'Review desteği',
                        'has_offers' => 'Teklif/fiyat bilgisi',
                        'custom_fields' => 'Özel alanlar'
                    ]
                ],
                'helper_description' => 'Google uyumlu, SEO optimizeli schema markup kodları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı schema markup',
                    'content' => 'Valid JSON-LD code',
                    'seo_impact' => 'SEO etki potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi telefon ürünü: marka, model, fiyat, stok durumu, müşteri yorumları, teknik özellikler, görseller.', 'label' => 'Product Schema'],
                    ['text' => 'İstanbul Kadıköy restoran: İtalyan mutfağı, açılış saatleri, adres, telefon, menü, rezervasyon, değerlendirmeler.', 'label' => 'Restaurant Schema'],
                    ['text' => 'Blog yazısı: SEO rehberi, yazar bilgisi, yayın tarihi, kategori, etiketler, okuma süresi, güncellenme tarihi.', 'label' => 'Article Schema']
                ],
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Meta Tag Optimizer
            [
                'name' => 'Meta Tag Optimizer',
                'slug' => 'meta-tag-optimizer',
                'description' => 'CTR\'yi artıran mükemmel meta title ve description\'lar.',
                'emoji' => '🏷️',
                'icon' => 'fas fa-tags',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 14,
                'badge_color' => 'success',
                'input_placeholder' => 'Sayfa içeriği ve hedef anahtar kelime...',
                'quick_prompt' => 'Sen meta tag optimizasyon uzmanısın, yüksek CTR (tıklama oranı) elde eden, Google arama sonuçlarında öne çıkan, kullanıcıların tıklamak isteyeceği meta title ve description oluşturan bir SEO uzmanısın. Verilen sayfa için optimum meta taglar oluştur.',
                'response_template' => [
                    'sections' => [
                        'META TITLE ÖNERİLERİ:',
                        '  Primary title (55-60 karakter)',
                        '  Alternative versions (3 varyasyon)',
                        '  Anahtar kelime konumu',
                        '  CTR trigger words',
                        'META DESCRIPTION:',
                        '  Primary description (150-160 karakter)',
                        '  Alternative versions (2 varyasyon)',
                        '  Call-to-action dahil',
                        '  Benefit-focused copy',
                        'ANAHTAR KELİME STRATEJİSİ:',
                        '  Primary keyword integration',
                        '  LSI keywords usage',
                        '  Natural keyword density',
                        '  Semantic relevance',
                        'CTR OPTİMİZASYONU:',
                        '  Emotion triggers',
                        '  Urgency indicators',
                        '  Benefit statements',
                        '  Social proof hints',
                        'TECHNICAL TAGS:',
                        '  Open Graph tags',
                        '  Twitter Card meta',
                        '  Canonical URL',
                        '  Hreflang (if applicable)',
                        'A/B TEST ÖNERİLERİ:',
                        '  Test edilecek elementler',
                        '  Varyasyon kriterleri',
                        '  Ölçüm metrikleri',
                        '  Test süresi önerisi',
                        'REKABET ANALİZİ:',
                        '  Competitor comparison',
                        '  Differentiation points',
                        '  Unique selling proposition',
                        '  Gap opportunities'
                    ],
                    'format' => 'SEO-optimized meta tags',
                    'scoring' => true
                ],
                'helper_function' => 'ai_meta_tag_optimizer',
                'helper_examples' => [
                    'homepage' => [
                        'code' => "ai_meta_tag_optimizer('hukuk bürosu anasayfa', ['keywords' => ['avukat', 'hukuk bürosu'], 'location' => 'İstanbul'])",
                        'description' => 'Ana sayfa meta tagları',
                        'estimated_tokens' => 200
                    ],
                    'product_page' => [
                        'code' => "ai_meta_tag_optimizer('e-ticaret ürün sayfası', ['product' => 'laptop', 'brand' => 'Apple', 'model' => 'MacBook'])",
                        'description' => 'Ürün sayfası meta tagları',
                        'estimated_tokens' => 180
                    ]
                ],
                'helper_parameters' => [
                    'page_content' => 'Sayfa içeriği ve amacı',
                    'options' => [
                        'keywords' => 'Hedef anahtar kelimeler',
                        'location' => 'Konum (yerel SEO için)',
                        'page_type' => 'Sayfa türü (homepage, product, category, blog)',
                        'brand' => 'Marka adı',
                        'competition' => 'Rekabet seviyesi'
                    ]
                ],
                'helper_description' => 'Yüksek CTR ve SEO performansı için optimize edilmiş meta taglar oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı meta tag optimizasyonu',
                    'content' => 'SEO-ready meta tags',
                    'ctr_score' => 'Tahmini CTR potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'İstanbul Anadolu yakasında aile hukuku, boşanma, miras hukuku alanlarında hizmet veren hukuk bürosu. 20 yıl deneyim, ücretsiz danışmanlık.', 'label' => 'Law Firm Homepage'],
                    ['text' => 'E-ticaret sitesi kadın giyim kategorisi: elbise, bluz, pantolon, etek. 500+ marka, ücretsiz kargo, 14 gün iade garantisi.', 'label' => 'E-commerce Category'],
                    ['text' => 'WordPress SEO rehberi blog yazısı: başlangıçtan ileri seviyeye kadar plugin\'ler, tema optimizasyonu, hız artırma teknikleri.', 'label' => 'Blog Article']
                ],
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // FAQ Generator
            [
                'name' => 'FAQ & SSS Üretici',
                'slug' => 'faq-generator',
                'description' => 'Müşteri sorularını önleyen kapsamlı SSS sayfaları.',
                'emoji' => '❓',
                'icon' => 'fas fa-question-circle',
                'category' => 'content-creation',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 15,
                'badge_color' => 'warning',
                'input_placeholder' => 'İşletme türü ve sık sorulan konular...',
                'quick_prompt' => 'Sen FAQ içerik uzmanısın, müşteri hizmetleri deneyimi olan, kullanıcıların gerçekten merak ettiği soruları bilen ve açık, anlaşılır yanıtlar veren bir müşteri deneyimi uzmanısın. Verilen işletme için kapsamlı SSS sayfası oluştur.',
                'response_template' => [
                    'sections' => [
                        'TEMEL SORULAR:',
                        '  İşletme/hizmet hakkında',
                        '  Nasıl çalışır temel bilgiler',
                        '  İletişim ve erişilebilirlik',
                        '  Çalışma saatleri ve konum',
                        'ÜRÜN/HİZMET SORULARI:',
                        '  Ürün özellikleri',
                        '  Hizmet kapsamı',
                        '  Kalite ve garanti',
                        '  Teknik detaylar',
                        'FİYATLANDIRMA:',
                        '  Fiyat politikası',
                        '  Ödeme yöntemleri',
                        '  İndirim ve kampanyalar',
                        '  Abonelik/üyelik koşulları',
                        'SİPARİŞ VE TESLİMAT:',
                        '  Sipariş süreci',
                        '  Teslimat süreleri',
                        '  Kargo maliyetleri',
                        '  Teslimat seçenekleri',
                        'İADE VE DEĞİŞİM:',
                        '  İade koşulları',
                        '  Değişim politikası',
                        '  Para iade süreci',
                        '  Hasar durumları',
                        'TEKNIK DESTEK:',
                        '  Sorun giderme',
                        '  Kurulum/kullanım',
                        '  Güncelleme/bakım',
                        '  Troubleshooting',
                        'GÜVENLİK VE GİZLİLİK:',
                        '  Veri güvenliği',
                        '  Gizlilik politikası',
                        '  Ödeme güvenliği',
                        '  Hesap korunması'
                    ],
                    'format' => 'Comprehensive FAQ page',
                    'scoring' => false
                ],
                'helper_function' => 'ai_faq_generator',
                'helper_examples' => [
                    'ecommerce' => [
                        'code' => "ai_faq_generator('e-ticaret sitesi', ['categories' => ['shipping', 'returns', 'payment'], 'business_type' => 'online_store'])",
                        'description' => 'E-ticaret SSS sayfası',
                        'estimated_tokens' => 400
                    ],
                    'saas' => [
                        'code' => "ai_faq_generator('SaaS platform', ['categories' => ['pricing', 'technical', 'billing'], 'target' => 'businesses'])",
                        'description' => 'SaaS ürün SSS',
                        'estimated_tokens' => 380
                    ]
                ],
                'helper_parameters' => [
                    'business_description' => 'İşletme türü ve hizmetler',
                    'options' => [
                        'categories' => 'Ana soru kategorileri',
                        'business_type' => 'İş modeli türü',
                        'target_audience' => 'Hedef müşteri kitlesi',
                        'complexity_level' => 'Teknik detay seviyesi',
                        'tone' => 'Yanıt tonu'
                    ]
                ],
                'helper_description' => 'Müşteri memnuniyetini artıran, kapsamlı SSS sayfaları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı SSS üretimi',
                    'content' => 'Comprehensive FAQ content',
                    'customer_satisfaction' => 'Müşteri memnuniyet potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Online mağaza: teknoloji ürünleri, kargo teslimat, iade değişim, 24/7 müşteri desteği, 14 gün iade garantisi, ücretsiz kargo kampanyaları.', 'label' => 'E-commerce Tech Store'],
                    ['text' => 'CRM yazılımı SaaS: abonelik planları, API entegrasyonu, veri güvenliği, teknik destek, migration hizmetleri, custom development.', 'label' => 'SaaS CRM Platform'],
                    ['text' => 'Diş kliniği: randevu sistemi, tedavi süreçleri, fiyat bilgileri, sigorta anlaşmaları, acil durumlar, kontrol randevuları.', 'label' => 'Dental Clinic']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // WhatsApp Business
            [
                'name' => 'WhatsApp Business Pro',
                'slug' => 'whatsapp-business-pro',
                'description' => 'WhatsApp Business için otomatik mesajlar ve kampanyalar.',
                'emoji' => '💬',
                'icon' => 'fab fa-whatsapp',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'text',
                'complexity_level' => 'beginner',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 16,
                'badge_color' => 'success',
                'input_placeholder' => 'Mesaj türü ve işletme bilgisi...',
                'quick_prompt' => 'Sen WhatsApp Business uzmanısın, kişisel ve samimi ama profesyonel mesajlar oluşturan, müşteri ilişkilerini güçlendiren, satış dönüşümü sağlayan WhatsApp iletişim uzmanısın. Verilen amaç için etkili WhatsApp mesajı oluştur.',
                'response_template' => [
                    'sections' => [
                        'MESAJ TİPİ ANALİZİ:',
                        '  Mesaj amacı ve hedefi',
                        '  Timing ve context',
                        '  Recipient personası',
                        '  Expected response',
                        'MESAJ İÇERİĞİ:',
                        '  Opening greeting',
                        '  Core message body',
                        '  Call-to-action',
                        '  Closing signature',
                        'TON VE STİL:',
                        '  Friendly professional tone',
                        '  Emoji kullanımı',
                        '  Personal touch elements',
                        '  Brand voice alignment',
                        'WHATSAPP ÖZELLİKLERİ:',
                        '  Media attachment önerileri',
                        '  Quick reply options',
                        '  Business catalog integration',
                        '  Contact/location sharing',
                        'OTOMASYON ENTEGRASYONu:',
                        '  Auto-reply triggers',
                        '  Follow-up sequence',
                        '  Conditional responses',
                        '  Escalation scenarios',
                        'ENGAGEMENT TAKTİKLERİ:',
                        '  Response encouragement',
                        '  Question prompts',
                        '  Value-driven content',
                        '  Urgency/scarcity hints'
                    ],
                    'format' => 'WhatsApp-ready business message',
                    'scoring' => false
                ],
                'helper_function' => 'ai_whatsapp_business_message',
                'helper_examples' => [
                    'welcome' => [
                        'code' => "ai_whatsapp_business_message('hoşgeldin mesajı', ['business_type' => 'restaurant', 'tone' => 'friendly'])",
                        'description' => 'Karşılama mesajı',
                        'estimated_tokens' => 150
                    ],
                    'order_update' => [
                        'code' => "ai_whatsapp_business_message('sipariş güncellemesi', ['status' => 'shipped', 'include_tracking' => true])",
                        'description' => 'Sipariş durumu bildirimi',
                        'estimated_tokens' => 120
                    ]
                ],
                'helper_parameters' => [
                    'message_purpose' => 'Mesaj amacı ve türü',
                    'options' => [
                        'business_type' => 'İşletme türü',
                        'tone' => 'Mesaj tonu (friendly, professional, casual)',
                        'include_media' => 'Medya eklentisi',
                        'automation_level' => 'Otomasyon seviyesi',
                        'target_action' => 'Hedeflenen aksiyon'
                    ]
                ],
                'helper_description' => 'WhatsApp Business için etkili, dönüşüm odaklı mesajlar oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı WhatsApp mesajı',
                    'content' => 'WhatsApp-ready message',
                    'engagement_score' => 'Etkileşim potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Yeni müşteri karşılama mesajı: restoran, rezervasyon sistemi tanıtımı, menü linkı paylaşımı, özel teklifler hakkında bilgilendirme.', 'label' => 'Welcome Message'],
                    ['text' => 'Sipariş hazırlandı bildirimi: e-ticaret, kargo takip numarası, tahmini teslimat saati, müşteri hizmetleri iletişim bilgileri.', 'label' => 'Order Update'],
                    ['text' => 'Randevu hatırlatma mesajı: güzellik salonu, yarınki randevu bilgileri, iptal/erteleme seçenekleri, salon adres ve yol tarifi.', 'label' => 'Appointment Reminder']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // LinkedIn Content
            [
                'name' => 'LinkedIn Thought Leader',
                'slug' => 'linkedin-thought-leader',
                'description' => 'LinkedIn\'de sektör lideri olmanızı sağlayan içerikler.',
                'emoji' => '💼',
                'icon' => 'fab fa-linkedin',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 17,
                'badge_color' => 'primary',
                'input_placeholder' => 'Uzmanlık alanı ve konu...',
                'quick_prompt' => 'Sen LinkedIn thought leadership uzmanısın, profesyonel ağlarda otorite kazandıran, sektör lideri imajı yaratan, iş dünyasında etkili içerikler oluşturan bir LinkedIn stratejistisin. Verilen uzmanlık alanı için thought leadership içeriği oluştur.',
                'response_template' => [
                    'sections' => [
                        'THOUGHT LEADERSHIP YAKLAŞIMI:',
                        '  Unique perspective positioning',
                        '  Industry insight angle',
                        '  Personal expertise demonstration',
                        '  Value-driven approach',
                        'CONTENT STRUCTURE:',
                        '  Attention-grabbing opener',
                        '  Personal experience/story',
                        '  Industry insights/data',
                        '  Practical takeaways',
                        '  Engaging conclusion with CTA',
                        'PROFESSIONAL TONE:',
                        '  Authoritative but approachable',
                        '  Data-backed statements',
                        '  Professional storytelling',
                        '  Industry-specific terminology',
                        'ENGAGEMENT ELEMENTS:',
                        '  Discussion starters',
                        '  Poll questions',
                        '  Experience sharing prompts',
                        '  Network advice requests',
                        'LINKEDIN OPTİMİZASYONU:',
                        '  Optimal post length (1300-1900 chars)',
                        '  Hashtag strategy (3-5 relevant)',
                        '  Mention strategy (@connections)',
                        '  Video/image integration tips',
                        'NETWORK BUILDING:',
                        '  Connection-worthy content',
                        '  Comment engagement tactics',
                        '  Collaborative post opportunities',
                        '  Industry conversation joining',
                        'METRICS VE PERFORMANS:',
                        '  Engagement tracking metrics',
                        '  Reach optimization tips',
                        '  Connection growth indicators',
                        '  Thought leadership KPIs'
                    ],
                    'format' => 'LinkedIn thought leadership content',
                    'scoring' => true
                ],
                'helper_function' => 'ai_linkedin_thought_leader',
                'helper_examples' => [
                    'tech_leader' => [
                        'code' => "ai_linkedin_thought_leader('teknoloji liderliği', ['industry' => 'tech', 'topic' => 'AI transformation', 'experience' => '10_years'])",
                        'description' => 'Teknoloji sektörü thought leadership',
                        'estimated_tokens' => 350
                    ],
                    'marketing_expert' => [
                        'code' => "ai_linkedin_thought_leader('pazarlama stratejisi', ['focus' => 'digital_marketing', 'audience' => 'CMOs'])",
                        'description' => 'Pazarlama uzmanlığı içeriği',
                        'estimated_tokens' => 320
                    ]
                ],
                'helper_parameters' => [
                    'expertise_area' => 'Uzmanlık alanı ve konu',
                    'options' => [
                        'industry' => 'Sektör',
                        'topic' => 'Spesifik konu',
                        'experience' => 'Deneyim seviyesi',
                        'audience' => 'Hedef profesyonel kitle',
                        'content_type' => 'İçerik türü (insight, story, advice)'
                    ]
                ],
                'helper_description' => 'LinkedIn\'de sektör otoritesi kazandıran thought leadership içerikleri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı thought leadership içeriği',
                    'content' => 'LinkedIn-optimized post',
                    'authority_score' => 'Otorite kazanım potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => '10 yıl yazılım geliştirme deneyimi, AI dönüşümü konusunda şirketlere danışmanlık, startup kurucu, teknoloji trendleri hakkında görüş paylaşımı.', 'label' => 'Tech Leadership'],
                    ['text' => 'Digital marketing direktörü, e-ticaret büyütme stratejileri, veri odaklı pazarlama kampanyaları, CMO\'lara tavsiyeleri.', 'label' => 'Marketing Expertise'],
                    ['text' => 'HR uzmanı, uzaktan çalışma kültürü, çalışan deneyimi optimizasyonu, modern işe alım stratejileri üzerine içerik.', 'label' => 'HR Thought Leadership']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Müşteri Hizmetleri Sihirbazı
            [
                'name' => 'Müşteri Hizmetleri Sihirbazı',
                'slug' => 'customer-service-wizard',
                'description' => 'Memnuniyet garantili müşteri iletişimi ve sorun çözümleri.',
                'emoji' => '🎭',
                'icon' => 'fas fa-headset',
                'category' => 'communication',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 18,
                'badge_color' => 'info',
                'input_placeholder' => 'Müşteri sorunu ve durum detayları...',
                'quick_prompt' => 'Sen müşteri hizmetleri uzmanısın, empati kurabilen, sorunları hızla çözen, müşteri memnuniyetini maksimize eden, profesyonel ama samimi iletişim kuran bir uzman müşteri temsilcisisin. Verilen durum için müşteri memnuniyeti odaklı çözüm sun.',
                'response_template' => [
                    'sections' => [
                        'DURUM ANALİZİ:',
                        '  Müşteri sorununun tanımı',
                        '  Aciliyet seviyesi',
                        '  Müşteri duygu durumu',
                        '  Beklenen çözüm süresi',
                        'EMPATİK YAKLAŞIM:',
                        '  Müşteriyi anlama ifadeleri',
                        '  Sorun kabul etme dili',
                        '  Sakin ve güven verici ton',
                        '  Kişisel ilgi gösterme',
                        'ÇÖZÜM STRATEJİSİ:',
                        '  Adım adım çözüm planı',
                        '  Alternatif seçenekler',
                        '  Zaman çizelgesi',
                        '  Responsibility ownership',
                        'İLETİşİM TAKTİKLERİ:',
                        '  Açık ve anlaşılır dil',
                        '  Teknik jargondan kaçınma',
                        '  Doğrulama ve onay isteme',
                        '  Süreç transparanlığı',
                        'ESKALASYoN YÖNETİMİ:',
                        '  Hangi durumlarda escalate',
                        '  Supervisor\'a geçiş protokolü',
                        '  Müşteri beklenti yönetimi',
                        '  Takip süreçleri',
                        'TAKİP VE KAPANIŞ:',
                        '  Çözüm sonrası kontrol',
                        '  Memnuniyet ölçümü',
                        '  Gelecek önlem önerileri',
                        '  İlişki güçlendirme fırsatları'
                    ],
                    'format' => 'Customer satisfaction-focused solution',
                    'scoring' => true
                ],
                'helper_function' => 'ai_customer_service_wizard',
                'helper_examples' => [
                    'product_issue' => [
                        'code' => "ai_customer_service_wizard('ürün arızası şikayeti', ['issue_type' => 'defective', 'urgency' => 'high', 'customer_mood' => 'frustrated'])",
                        'description' => 'Ürün arıza çözümü',
                        'estimated_tokens' => 300
                    ],
                    'billing_inquiry' => [
                        'code' => "ai_customer_service_wizard('fatura sorgusu', ['type' => 'billing', 'complexity' => 'medium'])",
                        'description' => 'Faturalandırma sorun çözümü',
                        'estimated_tokens' => 250
                    ]
                ],
                'helper_parameters' => [
                    'customer_issue' => 'Müşteri sorunu açıklaması',
                    'options' => [
                        'issue_type' => 'Sorun türü (product, billing, service, technical)',
                        'urgency' => 'Aciliyet seviyesi',
                        'customer_mood' => 'Müşteri ruh hali',
                        'complexity' => 'Sorun karmaşıklığı',
                        'channel' => 'İletişim kanalı'
                    ]
                ],
                'helper_description' => 'Müşteri memnuniyeti odaklı, empati kurarak sorun çözümleri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı müşteri hizmetleri çözümü',
                    'content' => 'Customer satisfaction solution',
                    'satisfaction_score' => 'Müşteri memnuniyet potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'E-ticaret müşterisi: sipariş ettiği telefon kutudan arızalı çıktı, 3 gündür kullanamıyor, çok sinirli durumda, hemen değişim istiyor.', 'label' => 'Product Defect'],
                    ['text' => 'SaaS müşterisi: faturasında anlamadığı ek ücretler var, açıklama istiyor, iptal tehdidi ediyor, billing team\'e yönlendirilmeli.', 'label' => 'Billing Issue'],
                    ['text' => 'Restoran müşterisi: rezervasyon kaydı yok, özel gün kutlaması için gelmiş, masa bulunamıyor, ailesi bekliyor.', 'label' => 'Service Problem']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // İş İlanı Yaratıcısı
            [
                'name' => 'İş İlanı Yaratıcısı',
                'slug' => 'job-posting-creator',
                'description' => 'En iyi adayları çeken, profesyonel iş ilanları.',
                'emoji' => '💼',
                'icon' => 'fas fa-briefcase',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 19,
                'badge_color' => 'warning',
                'input_placeholder' => 'Pozisyon adı, şirket ve gereksinimler...',
                'quick_prompt' => 'Sen İK uzmanısın, en kaliteli adayları çeken, şirket kültürünü yansıtan, net beklentileri belirten, profesyonel ve çekici iş ilanları oluşturan bir insan kaynakları uzmanısın. Verilen pozisyon için kapsamlı iş ilanı oluştur.',
                'response_template' => [
                    'sections' => [
                        'BAŞLIK VE POZİSYON:',
                        '  Çekici pozisyon başlığı',
                        '  Seniority level belirtimi',
                        '  Çalışma şekli (remote/hybrid/onsite)',
                        '  Lokasyon bilgisi',
                        'ŞİRKET TANITIMI:',
                        '  Şirket misyon/vizyon',
                        '  Şirket büyüklüğü ve sektör',
                        '  Şirket kültürü highlights',
                        '  Çalışma ortamı tanıtımı',
                        'POZİSYON DETAYLARI:',
                        '  Ana sorumluluklar (5-7 madde)',
                        '  Günlük iş akışı',
                        '  Takım yapısı ve raporlama',
                        '  Proje örnekleri',
                        'GEREKSİNİMLER:',
                        '  Zorunlu nitelikler',
                        '  Tercih edilen özellikler',
                        '  Teknik beceriler',
                        '  Soft skills',
                        '  Deneyim gereksinimleri',
                        'SUNDUKLARIMIZ:',
                        '  Maaş aralığı (şeffaf)',
                        '  Yan haklar ve benefits',
                        '  Kariyer gelişim fırsatları',
                        '  Eğitim ve gelişim destekleri',
                        '  Work-life balance olanakları',
                        'BAŞVURU SÜRECİ:',
                        '  Başvuru adımları',
                        '  Mülakat süreç detayları',
                        '  Süreç timeline\'ı',
                        '  Beklenen dokümantasyon',
                        'ÇEŞİTLİLİK VE İNKLÜZYON:',
                        '  Equal opportunity statement',
                        '  Diversity commitment',
                        '  Accessible workplace',
                        '  Inclusive culture emphasis'
                    ],
                    'format' => 'Professional job posting',
                    'scoring' => false
                ],
                'helper_function' => 'ai_job_posting_creator',
                'helper_examples' => [
                    'software_engineer' => [
                        'code' => "ai_job_posting_creator('Senior React Developer', ['company_size' => 'startup', 'remote' => true, 'experience' => '5+ years'])",
                        'description' => 'Yazılım geliştirici ilanı',
                        'estimated_tokens' => 400
                    ],
                    'marketing_manager' => [
                        'code' => "ai_job_posting_creator('Marketing Manager', ['industry' => 'ecommerce', 'team_size' => 5, 'salary_range' => '15000-20000'])",
                        'description' => 'Pazarlama müdürü ilanı',
                        'estimated_tokens' => 380
                    ]
                ],
                'helper_parameters' => [
                    'position_details' => 'Pozisyon adı ve temel detaylar',
                    'options' => [
                        'company_size' => 'Şirket büyüklüğü',
                        'industry' => 'Sektör',
                        'remote' => 'Uzaktan çalışma opsiyonu',
                        'experience' => 'Deneyim gereksinimi',
                        'salary_range' => 'Maaş aralığı',
                        'team_size' => 'Takım büyüklüğü'
                    ]
                ],
                'helper_description' => 'Kaliteli adayları çeken, kapsamlı ve profesyonel iş ilanları oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı iş ilanı',
                    'content' => 'Professional job posting',
                    'candidate_attraction' => 'Aday çekme potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Senior React Developer arıyoruz: 5+ yıl deneyim, TypeScript, Next.js, startup ortamı, remote çalışma, 25-35k maaş aralığı, hızlı büyüyen takım.', 'label' => 'Software Developer'],
                    ['text' => 'Marketing Manager pozisyonu: e-ticaret deneyimi, 5 kişilik pazarlama takımını yönetme, dijital kampanyalar, 15-20k maaş, hybrid çalışma.', 'label' => 'Marketing Manager'],
                    ['text' => 'UX/UI Designer: mobil app tasarım deneyimi, Figma/Sketch, user research, 3+ yıl deneyim, kreatif ajans ortamı, özel sağlık sigortası.', 'label' => 'UX Designer']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // LinkedIn Thought Leader Pro
            [
                'name' => 'LinkedIn Thought Leader Pro',
                'slug' => 'linkedin-thought-leader-pro',
                'description' => 'LinkedIn\'de sektör lideri olmanızı sağlayan içerikler.',
                'emoji' => '💼',
                'icon' => 'fab fa-linkedin',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'markdown',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 17,
                'badge_color' => 'primary',
                'input_placeholder' => 'Uzmanlık alanı ve konu...',
                'quick_prompt' => 'Sen LinkedIn thought leadership uzmanısın, profesyonel ağlarda otorite kazandıran, sektör lideri imajı yaratan, iş dünyasında etkili içerikler oluşturan bir LinkedIn stratejistisin. Verilen uzmanlık alanı için thought leadership içeriği oluştur.',
                'response_template' => [
                    'sections' => [
                        'THOUGHT LEADERSHIP YAKLAŞIMI:',
                        '  Unique perspective positioning',
                        '  Industry insight angle',
                        '  Personal expertise demonstration',
                        '  Value-driven approach',
                        'CONTENT STRUCTURE:',
                        '  Attention-grabbing opener',
                        '  Personal experience/story',
                        '  Industry insights/data',
                        '  Practical takeaways',
                        '  Engaging conclusion with CTA',
                        'PROFESSIONAL TONE:',
                        '  Authoritative but approachable',
                        '  Data-backed statements',
                        '  Professional storytelling',
                        '  Industry-specific terminology',
                        'ENGAGEMENT ELEMENTS:',
                        '  Discussion starters',
                        '  Poll questions',
                        '  Experience sharing prompts',
                        '  Network advice requests',
                        'LINKEDIN OPTİMİZASYONU:',
                        '  Optimal post length (1300-1900 chars)',
                        '  Hashtag strategy (3-5 relevant)',
                        '  Mention strategy (@connections)',
                        '  Video/image integration tips',
                        'NETWORK BUILDING:',
                        '  Connection-worthy content',
                        '  Comment engagement tactics',
                        '  Collaborative post opportunities',
                        '  Industry conversation joining',
                        'METRICS VE PERFORMANS:',
                        '  Engagement tracking metrics',
                        '  Reach optimization tips',
                        '  Connection growth indicators',
                        '  Thought leadership KPIs',
                        'CONTENT FORMATS:',
                        '  Text-only posts',
                        '  Document carousels',
                        '  Video insights',
                        '  Poll discussions',
                        'AUTHORITY BUILDING:',
                        '  Industry trend analysis',
                        '  Future predictions',
                        '  Best practice sharing',
                        '  Mistake/lesson learned stories'
                    ],
                    'format' => 'LinkedIn thought leadership content',
                    'scoring' => false
                ],
                'helper_function' => 'ai_linkedin_thought_leader',
                'helper_examples' => [
                    'b2b_sales' => [
                        'code' => "ai_linkedin_thought_leader('B2B satış stratejileri', ['industry' => 'technology', 'experience_level' => 'senior'])",
                        'description' => 'B2B satış uzmanı içeriği',
                        'estimated_tokens' => 350
                    ],
                    'leadership' => [
                        'code' => "ai_linkedin_thought_leader('uzaktan ekip yönetimi', ['industry' => 'consulting', 'role' => 'executive'])",
                        'description' => 'Liderlik ve yönetim içeriği',
                        'estimated_tokens' => 320
                    ]
                ],
                'helper_parameters' => [
                    'expertise_topic' => 'Uzmanlık alanı ve konu',
                    'options' => [
                        'industry' => 'Sektör',
                        'experience_level' => 'Deneyim seviyesi',
                        'role' => 'Pozisyon/rol',
                        'content_type' => 'İçerik türü',
                        'target_audience' => 'Hedef profesyonel kitle'
                    ]
                ],
                'helper_description' => 'LinkedIn\'de thought leadership ve otorite kazandıran profesyonel içerikler oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı thought leadership içeriği',
                    'content' => 'LinkedIn-optimized professional content',
                    'authority_score' => 'Otorite kazanım potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'B2B SaaS satış süreçlerinde AI\'ın rolü: lead scoring, customer journey analizi, personalized outreach. 15 yıllık satış deneyimi ve son 3 yılda %300 pipeline artışı.', 'label' => 'B2B Sales Strategy'],
                    ['text' => 'Uzaktan çalışma döneminde ekip liderliği: iletişim protokolleri, performans ölçümü, takım motivasyonu. Fortune 500 şirketinde 50 kişilik global ekip yönetimi deneyimi.', 'label' => 'Remote Leadership'],
                    ['text' => 'Dijital dönüşümde yaşanan en büyük hatalar: teknoloji odaklı yaklaşım, change management eksikliği. 20+ proje deneyimi ve $50M+ tasarruf sağlama hikayeleri.', 'label' => 'Digital Transformation']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // TikTok Content
            [
                'name' => 'TikTok Viral Factory',
                'slug' => 'tiktok-viral-factory',
                'description' => 'Milyonlarca izlenme alacak TikTok içerikleri.',
                'emoji' => '🎵',
                'icon' => 'fab fa-tiktok',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 18,
                'badge_color' => 'dark',
                'input_placeholder' => 'Video konsepti ve hedef kitle...',
                'quick_prompt' => 'Sen TikTok viral içerik uzmanısın, gençlerin ilgisini çeken, trend yaratan, algoritmanın sevdiği TikTok videoları konseptleri oluşturan bir uzmansın. Verilen konu için viral TikTok içeriği tasarla.',
                'response_template' => [
                    'sections' => [
                        'VİDEO KONSEPT:',
                        '  Ana fikir (hook stratejisi)',
                        '  Video süresi (15-60 saniye)',
                        '  Hedef demografik',
                        'SENARYO:',
                        '  İlk 3 saniye hook',
                        '  Ana içerik akışı',
                        '  Güçlü son (CTA)',
                        'VİZUEL ÖĞELER:',
                        '  Sahne düzeni',
                        '  Kostüm/aksesuar',
                        '  Işık ve renk paleti',
                        '  Text overlay önerileri',
                        'SES VE MÜZİK:',
                        '  Trending audio önerileri',
                        '  Ses efektleri',
                        '  Voiceover noktaları',
                        'HASHTAG STRATEJİSİ:',
                        '  Trending hashtagler',
                        '  Niche hashtagler',
                        '  Branded hashtagler',
                        'ENGAGİNG ELEMENTS:',
                        '  Yorum yaratacak sorular',
                        '  Challenge potential',
                        '  Duet bait stratejisi'
                    ],
                    'format' => 'TikTok-ready video concept',
                    'scoring' => false
                ],
                'helper_function' => 'ai_tiktok_viral_content',
                'helper_examples' => [
                    'comedy' => [
                        'code' => "ai_tiktok_viral_content('iş hayatı komedi', ['style' => 'comedy', 'duration' => '30sec'])",
                        'description' => 'İş hayatı komedisi TikTok konsepti',
                        'estimated_tokens' => 200
                    ],
                    'educational' => [
                        'code' => "ai_tiktok_viral_content('hızlı matematik hileleri', ['style' => 'educational', 'target_age' => '16-24'])",
                        'description' => 'Eğitici TikTok içeriği',
                        'estimated_tokens' => 250
                    ],
                    'lifestyle' => [
                        'code' => "ai_tiktok_viral_content('sabah rutini', ['style' => 'lifestyle', 'aesthetic' => 'minimal'])",
                        'description' => 'Yaşam tarzı içeriği',
                        'estimated_tokens' => 180
                    ]
                ],
                'helper_parameters' => [
                    'concept' => 'Video konsepti',
                    'options' => [
                        'style' => 'İçerik stili (comedy, educational, lifestyle, dance, challenge)',
                        'duration' => 'Video süresi (15sec, 30sec, 60sec)',
                        'target_age' => 'Hedef yaş grubu (13-17, 18-24, 25-34)',
                        'aesthetic' => 'Görsel estetik (minimal, colorful, dark, vintage)',
                        'trending_topic' => 'Güncel trend konusu',
                        'include_challenge' => 'Challenge öğesi ekle (true/false)',
                        'language' => 'Dil (Turkish varsayılan)'
                    ]
                ],
                'helper_description' => 'TikTok algoritmasını hackleyen, viral potansiyeli yüksek video konseptleri oluşturur. Trend analizi ve engagement optimizasyonu içerir.',
                'helper_returns' => [
                    'success' => 'Başarılı konsept üretimi',
                    'content' => 'TikTok video konsepti',
                    'tokens_used' => 'Kullanılan token sayısı',
                    'viral_score' => [
                        'trend_alignment' => 'Trend uyum puanı',
                        'engagement_potential' => 'Etkileşim potansiyeli',
                        'hook_strength' => 'Hook gücü',
                        'shareability' => 'Paylaşılabilirlik'
                    ]
                ],
                'example_inputs' => [
                    ['text' => 'İş yerinde stresli anlar: Patronla toplantı esnasında kafanızdan geçenler vs gerçekte söyledikleriniz. İkili ekran tekniği ile çekeceğim, sol tarafta gerçek ben, sağ tarafta kafamdaki ben.', 'label' => 'İş Hayatı Komedisi'],
                    ['text' => 'Türk mutfağından 60 saniyede baklava yapımı. Hamur açma hileleri, iç harç sırları, şerbet yakma püf noktaları. Hijyen eldivenleri ile aesthetic çekim yapacağım.', 'label' => 'Hızlı Tarif'],
                    ['text' => 'Sabah 5:30 kalkan birinin günlük rutini: Meditasyon, egzersiz, kitap okuma, healthy breakfast. Minimal estetik, soft müzik, time-lapse çekimler.', 'label' => 'Motivasyon Rutini'],
                    ['text' => 'Üniversiteye yeni başlayanlar için sosyalleşme ipuçları: Arkadaş edinme, kulüplere katılma, kampüste kaybolmama taktikleri. Gerçek deneyim paylaşacağım.', 'label' => 'Eğitim İçeriği'],
                    ['text' => 'Dikiş makinesi ile vintage kıyafet upcycling: Eski tişörtü crop top\'a çevirme. Step by step process, before/after dramatic reveal ile bitiş.', 'label' => 'DIY & Moda']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Press Release
            [
                'name' => 'Basın Bülteni Uzmanı',
                'slug' => 'press-release-expert',
                'description' => 'Medyanın ilgisini çekecek profesyonel basın bültenleri.',
                'emoji' => '📰',
                'icon' => 'fas fa-newspaper',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 19,
                'badge_color' => 'info',
                'input_placeholder' => 'Haber değeri taşıyan konu ve detaylar...',
                'quick_prompt' => 'Sen basın bülteni uzmanısın, medyanın ilgisini çeken, editörlerin yayınlamak isteyeceği, profesyonel basın bültenleri yazan bir uzmansın. Verilen konu için medya etkisi yüksek basın bülteni hazırla.',
                'response_template' => [
                    'sections' => [
                        'BAŞLIK: (Haber değeri yüksek, 80-100 karakter)',
                        '  Ana başlık',
                        '  Alt başlık (destekleyici)',
                        'LEAD PARAGRAF: (En önemli 5W1H)',
                        '  Kim, ne, nerede, ne zaman, neden, nasıl',
                        'ANA İÇERİK:',
                        '  Detaylı açıklama',
                        '  Sayısal veriler',
                        '  Alıntılar (CEO, uzman)',
                        '  Arka plan bilgisi',
                        'ŞİRKET BİLGİLERİ:',
                        '  Kurum tanıtımı',
                        '  İletişim bilgileri',
                        '  Web ve sosyal medya',
                        'MEDYA KİTİ:',
                        '  Yüksek çözünürlük görseller',
                        '  Röportaj imkanları',
                        '  Ek bilgi kaynakları',
                        'YAYINLAMA TALİMATLARI:',
                        '  Embargo tarihi',
                        '  Hedef medya listesi',
                        '  Follow-up stratejisi'
                    ],
                    'format' => 'Professional press release',
                    'scoring' => false
                ],
                'helper_function' => 'ai_press_release',
                'helper_examples' => [
                    'product_launch' => [
                        'code' => "ai_press_release('yeni mobil uygulama lansmanı', ['company' => 'TechStartup A.Ş.', 'launch_date' => '15 Şubat 2025'])",
                        'description' => 'Ürün lansmanı basın bülteni',
                        'estimated_tokens' => 400
                    ],
                    'partnership' => [
                        'code' => "ai_press_release('stratejik ortaklık anlaşması', ['companies' => ['ABC Corp', 'XYZ Ltd'], 'deal_value' => '50 milyon TL'])",
                        'description' => 'Ortaklık duyuru bülteni',
                        'estimated_tokens' => 350
                    ],
                    'award' => [
                        'code' => "ai_press_release('sektör ödülü kazanımı', ['award_name' => 'Yılın En İnovatif Şirketi', 'organization' => 'Teknoloji Derneği'])",
                        'description' => 'Ödül kazanımı bülteni',
                        'estimated_tokens' => 300
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Basın bülteni konusu',
                    'options' => [
                        'company' => 'Şirket adı',
                        'industry' => 'Sektör',
                        'target_media' => 'Hedef medya (national, local, tech, business)',
                        'urgency' => 'Aciliyet seviyesi (immediate, standard, planned)',
                        'embargo_date' => 'Yayın embargo tarihi',
                        'contact_person' => 'İletişim kişisi',
                        'language' => 'Dil (Turkish varsayılan)',
                        'tone' => 'Ton (formal, exciting, professional)'
                    ]
                ],
                'helper_description' => 'Medyanın dikkatini çeken, editörlerin yayınlamak isteyeceği profesyonel basın bültenleri oluşturur. Haber değeri optimizasyonu içerir.',
                'helper_returns' => [
                    'success' => 'Başarılı bülten üretimi',
                    'content' => 'Medyaya hazır basın bülteni',
                    'tokens_used' => 'Kullanılan token sayısı',
                    'media_score' => [
                        'news_value' => 'Haber değeri puanı',
                        'headline_strength' => 'Başlık gücü',
                        'quote_quality' => 'Alıntı kalitesi',
                        'publication_potential' => 'Yayınlanma potansiyeli'
                    ]
                ],
                'example_inputs' => [
                    ['text' => 'Yerli elektrikli araç şarj istasyonu üreticisi olan firmamız, 500 milyon TL yatırımla Türkiye\'nin en büyük şarj istasyonu ağını kurmaya başlıyor. İlk etapta 50 şehirde 2000 şarj noktası hedefliyoruz.', 'label' => 'Teknoloji Yatırımı'],
                    ['text' => 'Organik gıda zinciri olarak Almanya pazarına açılıyoruz. Berlin\'de ilk mağazamızı açarak Türk organik ürünlerini Avrupa\'ya tanıtacağız. 5 yılda 50 mağaza hedefliyoruz.', 'label' => 'Uluslararası Genişleme'],
                    ['text' => 'Eğitim teknolojileri şirketimiz "Yılın En İnovatif Eğitim Uygulaması" ödülünü kazandı. UNESCO destekli projemiz 100.000 öğrenciye ulaştı. Pandemi döneminde %300 büyüme kaydettik.', 'label' => 'Ödül ve Başarı'],
                    ['text' => 'Sağlık sektöründe yapay zeka destekli teşhis platformu geliştirdik. Radyoloji görüntülerini %95 doğrulukla analiz ediyor. 25 hastane ile pilot çalışma başlatıyoruz.', 'label' => 'Sağlık İnovasyonu'],
                    ['text' => 'Sürdürülebilirlik alanında Global Compact Turkey ile ortaklık kurduk. Karbon nötr üretim hedefimizi 2030\'a çektik. Sektörde ilk yeşil sertifikalı fabrika açılışını yapacağız.', 'label' => 'Sürdürülebilirlik']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Case Study
            [
                'name' => 'Vaka Analizi Yazarı',
                'slug' => 'case-study-writer',
                'description' => 'Satışları artıran ikna edici başarı hikayeleri.',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-line',
                'category' => 'content-creation',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 20,
                'badge_color' => 'success',
                'input_placeholder' => 'Proje detayları ve sonuçlar...',
                'quick_prompt' => 'Sen case study uzmanısın, müşteri başarı hikayelerini ikna edici şekilde anlatan, sayısal verilerle desteklenmiş, satış artırıcı vaka analizleri yazan bir uzmansın. Verilen proje için etkileyici başarı hikayesi oluştur.',
                'response_template' => [
                    'sections' => [
                        'YÖNETİCİ ÖZETİ:',
                        '  Ana sonuçlar (3-4 madde)',
                        '  ROI ve metriklerin özeti',
                        '  Proje süresi ve kapsamı',
                        'MÜŞTERİ PROFİLİ:',
                        '  Şirket tanıtımı',
                        '  Sektör ve büyüklük',
                        '  Coğrafi konum',
                        '  Önceki durumu',
                        'ZORLUK VE PROBLEM:',
                        '  Ana problemin tanımı',
                        '  Business impact',
                        '  Önceki çözüm denemeleri',
                        '  Aciliyet faktörleri',
                        'ÇÖZÜM VE STRATEJİ:',
                        '  Uygulanan çözüm detayları',
                        '  Implementation süreci',
                        '  Kullanılan teknolojiler/metodlar',
                        '  Proje timeline',
                        'SONUÇLAR VE BAŞARILER:',
                        '  Sayısal iyileştirmeler (%ler)',
                        '  KPI değişimleri',
                        '  ROI hesaplaması',
                        '  Beklenmeyen faydalar',
                        'ÖLÇÜMLENEN ETKILER:',
                        '  Before/after karşılaştırması',
                        '  Zaman serisi grafikleri',
                        '  Benchmark karşılaştırmaları',
                        'MÜŞTERİ YORUMLARI:',
                        '  Anahtar stakeholder alıntıları',
                        '  Kullanıcı deneyim yorumları',
                        '  Referans izni',
                        'GELECEK PLANLAR:',
                        '  Sürdürülebilir iyileştirmeler',
                        '  Ek proje fırsatları',
                        '  Uzun vadeli ortaklık'
                    ],
                    'format' => 'Professional case study',
                    'scoring' => true
                ],
                'helper_function' => 'ai_case_study_writer',
                'helper_examples' => [
                    'ecommerce_growth' => [
                        'code' => "ai_case_study_writer('e-ticaret dönüşüm artışı', ['client' => 'fashion retailer', 'improvement' => '250% conversion increase'])",
                        'description' => 'E-ticaret başarı hikayesi',
                        'estimated_tokens' => 450
                    ],
                    'b2b_sales' => [
                        'code' => "ai_case_study_writer('B2B satış süreç optimizasyonu', ['industry' => 'software', 'result' => '180% pipeline growth'])",
                        'description' => 'B2B satış case study',
                        'estimated_tokens' => 400
                    ]
                ],
                'helper_parameters' => [
                    'project_description' => 'Proje açıklaması ve sonuçları',
                    'options' => [
                        'client' => 'Müşteri profili',
                        'industry' => 'Sektör',
                        'timeline' => 'Proje süresi',
                        'improvement' => 'Ana iyileştirme metrikleri',
                        'challenge' => 'Ana zorluk',
                        'solution' => 'Çözüm yaklaşımı'
                    ]
                ],
                'helper_description' => 'Müşteri başarı hikayelerini etkileyici case study formatında oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı case study',
                    'content' => 'Professional success story',
                    'credibility_score' => 'İnanılırlık puanı'
                ],
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi için uyguladığımız UX/UI iyileştirmeleri sayesinde 6 ayda %250 dönüşüm artışı sağladık. Mobil responsive tasarım, checkout sürecini basitleştirme, kargo seçenekleri çeşitlendirme.', 'label' => 'E-commerce UX Success'],
                    ['text' => 'B2B yazılım şirketi için CRM entegrasyonu ve sales automation uyguladık. Lead response time %80 düştü, pipeline %180 arttı. 12 aylık süreçte 50 kişilik satış ekibi verimliliği 3 katına çıktı.', 'label' => 'B2B Sales Automation'],
                    ['text' => 'Hastane için dijital dönüşüm projesi: randevu sistemi, hasta takip, telemedicine entegrasyonu. Hasta memnuniyeti %90\'a çıktı, bekleme süreleri %60 azaldı, operational cost %30 düştü.', 'label' => 'Healthcare Digital Transformation']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Podcast Script
            [
                'name' => 'Podcast Senaryo Ustası',
                'slug' => 'podcast-script-master',
                'description' => 'Dinleyicileri ekrana kilitleyen podcast senaryoları.',
                'emoji' => '🎙️',
                'icon' => 'fas fa-microphone-alt',
                'category' => 'creative',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 21,
                'badge_color' => 'purple',
                'input_placeholder' => 'Podcast konusu ve format bilgisi...',
                'quick_prompt' => 'Sen podcast senaryo uzmanısın, dinleyicileri ekrana kilitleyen, ilgi çekici audio içerikler oluşturan, podcast formatlarında deneyimli bir içerik yaratıcısısın. Verilen konu için captivating podcast senaryosu oluştur.',
                'response_template' => [
                    'sections' => [
                        'PODCAST YAPISI:',
                        '  Episode formatı (solo, interview, panel)',
                        '  Tahmini süre',
                        '  Segment breakdown',
                        '  Intro/outro planı',
                        'HOOK & OPENING:',
                        '  Dikkat çekici açılış (30 saniye)',
                        '  Episode teaser',
                        '  Host introduction',
                        '  Guest introduction (if applicable)',
                        'ANA İÇERİK AKIŞı:',
                        '  Segment 1: Topic introduction',
                        '  Segment 2: Deep dive discussion',
                        '  Segment 3: Practical insights',
                        '  Segment 4: Key takeaways',
                        'INTERVIEW SORULARI:',
                        '  Warm-up questions',
                        '  Core topic questions',
                        '  Personal insight questions',
                        '  Future-looking questions',
                        'INTERAKTIF ÖĞELER:',
                        '  Listener call-outs',
                        '  Social media integration',
                        '  Q&A segments',
                        '  Poll/survey mentions',
                        'TRANSİSYON CÜMLE VE MÜZİK:',
                        '  Segment geçiş cümleleri',
                        '  Music cue points',
                        '  Ad break timing',
                        '  Energy level management',
                        'CLOSING & CTA:',
                        '  Episode summary',
                        '  Key takeaway emphasis',
                        '  Next episode tease',
                        '  Subscribe/review requests',
                        '  Contact information',
                        'SHOW NOTES ÖNERİLERİ:',
                        '  Time-stamped topics',
                        '  Guest links and resources',
                        '  Mentioned books/tools',
                        '  Social media handles'
                    ],
                    'format' => 'Professional podcast script',
                    'scoring' => false
                ],
                'helper_function' => 'ai_podcast_script_master',
                'helper_examples' => [
                    'entrepreneur_interview' => [
                        'code' => "ai_podcast_script_master('startup founder röportajı', ['format' => 'interview', 'duration' => '45min', 'guest_expertise' => 'e-commerce'])",
                        'description' => 'Girişimci röportaj scripti',
                        'estimated_tokens' => 500
                    ],
                    'tech_news' => [
                        'code' => "ai_podcast_script_master('haftalık teknoloji haberleri', ['format' => 'solo', 'duration' => '20min', 'topics' => ['AI', 'blockchain', 'cybersecurity']])",
                        'description' => 'Teknoloji haber podcast',
                        'estimated_tokens' => 400
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Podcast konusu ve teması',
                    'options' => [
                        'format' => 'Format türü (solo, interview, panel, roundtable)',
                        'duration' => 'Episode süresi',
                        'audience' => 'Hedef dinleyici kitlesi',
                        'tone' => 'Podcast tonu (casual, professional, educational)',
                        'expertise_level' => 'İçerik seviyesi',
                        'guest_info' => 'Konuk bilgileri (if applicable)'
                    ]
                ],
                'helper_description' => 'Engaging ve professional podcast senaryoları oluşturur, dinleyici katılımını artırır.',
                'helper_returns' => [
                    'success' => 'Başarılı podcast senaryosu',
                    'content' => 'Ready-to-record script',
                    'engagement_score' => 'Dinleyici katılım potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'E-ticaret startup kurucusu ile röportaj: Sıfırdan $1M ARR\'ye ulaşma hikayesi, pazarlama stratejileri, scaling zorlukları, yatırımcı ilişkileri. 45 dakikalık deep dive interview.', 'label' => 'Startup Success Interview'],
                    ['text' => 'Haftalık teknoloji özeti podcast: AI gelişmeleri, yeni ürün lansmanları, cybersecurity haberleri, kripto market analizi. 20 dakikalık solo format, güncel verilerle.', 'label' => 'Tech News Weekly'],
                    ['text' => 'Uzaktan çalışma best practices: productivity tips, team management, work-life balance. Remote work uzmanı ile sohbet, dinleyici sorularını da dahil edeceğiz.', 'label' => 'Remote Work Discussion']
                ],
                'prompts' => [
                    ['name' => 'Yaratıcı İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Landing Page Copy
            [
                'name' => 'Landing Page Mimarı',
                'slug' => 'landing-page-architect',
                'description' => 'Yüksek dönüşümlü landing page metinleri ve yapısı.',
                'emoji' => '🎯',
                'icon' => 'fas fa-bullseye',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 22,
                'badge_color' => 'danger',
                'input_placeholder' => 'Ürün/hizmet ve kampanya detayları...',
                'quick_prompt' => 'Sen landing page optimizasyon uzmanısın, yüksek conversion rate elde eden, ziyaretçileri aksiyona geçiren, A/B test edilmiş landing page copy\'si yazan bir dönüşüm uzmanısın. Verilen kampanya için high-converting landing page oluştur.',
                'response_template' => [
                    'sections' => [
                        'HERO SECTİON:',
                        '  Attention-grabbing headline',
                        '  Compelling sub-headline',
                        '  Primary CTA button',
                        '  Hero image/video önerisi',
                        '  Trust indicators (badges, testimonials)',
                        'VALUE PROPOSITION:',
                        '  Core benefit statement',
                        '  Unique selling points (3-5)',
                        '  Problem-solution fit',
                        '  Competitive advantages',
                        'SOCIAL PROOF:',
                        '  Customer testimonials',
                        '  Company logos',
                        '  Usage statistics',
                        '  Reviews/ratings',
                        '  Case study highlights',
                        'FEATURES & BENEFITS:',
                        '  Feature highlights',
                        '  Benefit-focused descriptions',
                        '  Visual feature breakdown',
                        '  ROI/value demonstration',
                        'OBJECTION HANDLING:',
                        '  Common concerns addressed',
                        '  Risk mitigation (guarantees)',
                        '  FAQ section',
                        '  Security/privacy assurances',
                        'URGENCY & SCARCITY:',
                        '  Limited time offers',
                        '  Stock/availability counters',
                        '  Bonus offerings',
                        '  FOMO triggers',
                        'CONVERSION FORM:',
                        '  Form field optimization',
                        '  Progressive disclosure',
                        '  Error handling',
                        '  Privacy statements',
                        'TECHNICAL ELEMENTS:',
                        '  Mobile responsiveness',
                        '  Page load optimization',
                        '  Analytics tracking',
                        '  A/B testing suggestions'
                    ],
                    'format' => 'High-converting landing page',
                    'scoring' => true
                ],
                'helper_function' => 'ai_landing_page_architect',
                'helper_examples' => [
                    'saas_trial' => [
                        'code' => "ai_landing_page_architect('CRM yazılımı ücretsiz deneme', ['offer' => '14-day trial', 'target' => 'small business'])",
                        'description' => 'SaaS deneme sayfası',
                        'estimated_tokens' => 450
                    ],
                    'webinar_signup' => [
                        'code' => "ai_landing_page_architect('pazarlama webinarı', ['topic' => 'social media marketing', 'speaker' => 'industry expert'])",
                        'description' => 'Webinar kayıt sayfası',
                        'estimated_tokens' => 400
                    ]
                ],
                'helper_parameters' => [
                    'campaign_description' => 'Kampanya ve teklif detayları',
                    'options' => [
                        'offer' => 'Ana teklif (free trial, discount, lead magnet)',
                        'target_audience' => 'Hedef kitle',
                        'industry' => 'Sektör',
                        'urgency' => 'Aciliyet seviyesi',
                        'conversion_goal' => 'Dönüşüm hedefi',
                        'competitor' => 'Ana rakipler'
                    ]
                ],
                'helper_description' => 'Conversion-optimized landing page copy\'si ve yapısı oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı landing page',
                    'content' => 'Conversion-ready page structure',
                    'conversion_score' => 'Tahmini dönüşüm potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'CRM yazılımı 14 günlük ücretsiz deneme: küçük işletmeler için müşteri takip sistemi, lead management, email automation. Kredi kartı bilgisi gerektirmez.', 'label' => 'SaaS Free Trial'],
                    ['text' => '\'Digital Marketing 2025\' webinarı: sosyal medya stratejileri, influencer marketing, ROI ölçümü. Canlı Q&A, sertifika ve kayıt hediyeli.', 'label' => 'Marketing Webinar'],
                    ['text' => '\'SEO Rehberi E-book\' indirme: 50 sayfalık kapsamlı guide, keyword research, backlink stratejileri, teknik SEO. Email ile anında teslim.', 'label' => 'Lead Magnet E-book']
                ],
                'prompts' => [
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Newsletter Content
            [
                'name' => 'Newsletter İçerik Editörü',
                'slug' => 'newsletter-content-editor',
                'description' => 'Açılıp okunmayı garantileyen newsletter içerikleri.',
                'emoji' => '📮',
                'icon' => 'fas fa-mail-bulk',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 23,
                'badge_color' => 'primary',
                'input_placeholder' => 'Newsletter konusu ve sıklığı...',
                'quick_prompt' => 'Sen newsletter editörü uzmanısın, okuyucuların açıp okumak istediği, engaging newsletter içerikleri oluşturan, email marketing performansını artıran bir uzman editörsün. Verilen konu için etkileyici newsletter oluştur.',
                'response_template' => [
                    'sections' => [
                        'EMAIL KONU SATIRI:',
                        '  Primary subject line (açılma odaklı)',
                        '  Alternative versions (A/B test)',
                        '  Preview text optimization',
                        'HEADER SECTION:',
                        '  Newsletter branding',
                        '  Issue number/date',
                        '  Warm greeting',
                        '  Quick issue overview',
                        'ANA İÇERİK BLOKLARI:',
                        '  Top story/feature article',
                        '  News highlights (3-5 items)',
                        '  Industry insights',
                        '  Company updates',
                        '  Expert tips/advice',
                        'VISUAL ELEMENTS:',
                        '  Image placements',
                        '  Graphics/infographics',
                        '  Brand consistency',
                        '  Mobile optimization',
                        'ENGAGİNG FEATURES:',
                        '  Interactive elements',
                        '  Poll/survey integration',
                        '  Social media links',
                        '  Community highlights',
                        'CALL-TO-ACTION:',
                        '  Primary CTA (newsletter specific)',
                        '  Secondary CTAs',
                        '  Website traffic drivers',
                        '  Social engagement prompts',
                        'FOOTER SECTION:',
                        '  Contact information',
                        '  Social media icons',
                        '  Unsubscribe link',
                        '  Forward to friend option',
                        'METRICS OPTIMIZATION:',
                        '  Open rate factors',
                        '  Click-through rate drivers',
                        '  Engagement metrics',
                        '  Deliverability considerations'
                    ],
                    'format' => 'Newsletter-ready content',
                    'scoring' => true
                ],
                'helper_function' => 'ai_newsletter_content_editor',
                'helper_examples' => [
                    'tech_weekly' => [
                        'code' => "ai_newsletter_content_editor('haftalık teknoloji özeti', ['frequency' => 'weekly', 'audience' => 'tech professionals'])",
                        'description' => 'Teknoloji newsletter',
                        'estimated_tokens' => 400
                    ],
                    'company_news' => [
                        'code' => "ai_newsletter_content_editor('şirket haberleri', ['type' => 'internal', 'audience' => 'employees'])",
                        'description' => 'Kurumsal newsletter',
                        'estimated_tokens' => 350
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Newsletter konusu ve teması',
                    'options' => [
                        'frequency' => 'Yayın sıklığı (weekly, monthly, bi-weekly)',
                        'audience' => 'Hedef okuyucu kitlesi',
                        'type' => 'Newsletter türü (promotional, informational, internal)',
                        'industry' => 'Sektör',
                        'tone' => 'İçerik tonu'
                    ]
                ],
                'helper_description' => 'Yüksek açılma ve tıklama oranları elde eden newsletter içerikleri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı newsletter içeriği',
                    'content' => 'Newsletter-ready content',
                    'engagement_score' => 'Okuyucu etkileşim potansiyeli'
                ],
                'example_inputs' => [
                    ['text' => 'Haftalık teknoloji haberleri newsletter: AI gelişmeleri, startup haberleri, tech trend analizleri, sektör raporları. Teknoloji profesyonelleri hedef kitle.', 'label' => 'Tech Weekly'],
                    ['text' => 'Şirket içi aylık newsletter: proje güncellemeleri, yeni çalışan tanıtımları, başarı hikayeleri, etkinlik duyuruları, CEO mesajı. Çalışanlar için motivasyonel.', 'label' => 'Internal Newsletter'],
                    ['text' => 'E-ticaret promosyonel newsletter: yeni ürün lansmanları, özel indirimler, müşteri yorumları, stil önerileri. Fashion-forward müşteriler hedef.', 'label' => 'E-commerce Promo']
                ],
                'prompts' => [
                    ['name' => 'Email Pazarlama Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ],

            // Press Release Pro
            [
                'name' => 'Basın Bülteni Pro',
                'slug' => 'press-release-pro',
                'description' => 'Medyanın ilgisini çekecek profesyonel basın bültenleri.',
                'emoji' => '📰',
                'icon' => 'fas fa-newspaper',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 21,
                'badge_color' => 'info',
                'input_placeholder' => 'Haber değeri taşıyan konu ve detaylar...',
                'quick_prompt' => 'Sen basın bülteni uzmanısın, medyanın ilgisini çeken, editörlerin yayınlamak isteyeceği, profesyonel basın bültenleri yazan bir uzmansın. Verilen konu için medya etkisi yüksek basın bülteni hazırla.',
                'response_template' => [
                    'sections' => [
                        'BAŞLIK: (Haber değeri yüksek, 80-100 karakter)',
                        '  Ana başlık',
                        '  Alt başlık (destekleyici)',
                        'LEAD PARAGRAF: (En önemli 5W1H)',
                        '  Kim, ne, nerede, ne zaman, neden, nasıl',
                        'ANA İÇERİK:',
                        '  Detaylı açıklama',
                        '  Sayısal veriler',
                        '  Alıntılar (CEO, uzman)',
                        '  Arka plan bilgisi',
                        'ŞİRKET BİLGİLERİ:',
                        '  Kurum tanıtımı',
                        '  İletişim bilgileri',
                        '  Web ve sosyal medya',
                        'MEDYA KİTİ:',
                        '  Yüksek çözünürlük görseller',
                        '  Röportaj imkanları',
                        '  Ek bilgi kaynakları'
                    ],
                    'format' => 'Professional press release',
                    'scoring' => false
                ],
                'helper_function' => 'ai_press_release_expert',
                'helper_examples' => [
                    'product_launch' => [
                        'code' => "ai_press_release_expert('yeni mobil uygulama lansmanı', ['company' => 'TechStartup A.Ş.', 'launch_date' => '15 Şubat 2025'])",
                        'description' => 'Ürün lansmanı basın bülteni',
                        'estimated_tokens' => 400
                    ],
                    'partnership' => [
                        'code' => "ai_press_release_expert('stratejik ortaklık anlaşması', ['companies' => ['ABC Corp', 'XYZ Ltd']])",
                        'description' => 'Ortaklık duyuru bülteni',
                        'estimated_tokens' => 350
                    ]
                ],
                'helper_parameters' => [
                    'topic' => 'Basın bülteni konusu',
                    'options' => [
                        'company' => 'Şirket adı',
                        'industry' => 'Sektör',
                        'target_media' => 'Hedef medya',
                        'urgency' => 'Aciliyet seviyesi',
                        'contact_person' => 'İletişim kişisi'
                    ]
                ],
                'helper_description' => 'Medyanın dikkatini çeken, profesyonel basın bültenleri oluşturur.',
                'helper_returns' => [
                    'success' => 'Başarılı bülten üretimi',
                    'content' => 'Medyaya hazır basın bülteni',
                    'media_score' => 'Haber değeri puanı'
                ],
                'example_inputs' => [
                    ['text' => 'Yerli elektrikli araç şarj istasyonu üreticisi, 500 milyon TL yatırımla Türkiye\'nin en büyük şarj istasyonu ağını kuruyor.', 'label' => 'Teknoloji Yatırımı'],
                    ['text' => 'Organik gıda zinciri Almanya pazarına açılıyor. Berlin\'de ilk mağaza açılışı, 5 yılda 50 mağaza hedefi.', 'label' => 'Uluslararası Genişleme']
                ],
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                ],
            ]
        ];

        foreach ($features as $featureData) {
            // Feature oluştur
            $feature = AIFeature::create([
                'name' => $featureData['name'],
                'slug' => $featureData['slug'],
                'description' => $featureData['description'],
                'emoji' => $featureData['emoji'],
                'icon' => $featureData['icon'],
                'category' => $featureData['category'],
                'response_length' => $featureData['response_length'],
                'response_format' => $featureData['response_format'],
                'complexity_level' => $featureData['complexity_level'],
                'status' => $featureData['status'],
                'is_system' => $featureData['is_system'],
                'is_featured' => $featureData['is_featured'] ?? false,
                'show_in_examples' => $featureData['show_in_examples'] ?? false,
                'sort_order' => $featureData['sort_order'],
                'badge_color' => $featureData['badge_color'],
                'input_placeholder' => $featureData['input_placeholder'],
                
                // YENİ TEMPLATE SİSTEMİ ALANLARI
                'quick_prompt' => $featureData['quick_prompt'] ?? null,
                'response_template' => isset($featureData['response_template']) ? json_encode($featureData['response_template']) : null,
                'helper_function' => $featureData['helper_function'] ?? null,
                'helper_examples' => isset($featureData['helper_examples']) ? json_encode($featureData['helper_examples']) : null,
                'helper_parameters' => isset($featureData['helper_parameters']) ? json_encode($featureData['helper_parameters']) : null,
                'helper_description' => $featureData['helper_description'] ?? null,
                'helper_returns' => isset($featureData['helper_returns']) ? json_encode($featureData['helper_returns']) : null,
                'example_inputs' => isset($featureData['example_inputs']) ? json_encode($featureData['example_inputs']) : null,
            ]);

            $this->command->info("Feature oluşturuldu: {$feature->name}");

            // Feature-Prompt ilişkilerini oluştur
            if (isset($featureData['prompts'])) {
                foreach ($featureData['prompts'] as $promptData) {
                    $prompt = Prompt::where('name', $promptData['name'])->first();
                    if ($prompt) {
                        AIFeaturePrompt::create([
                            'feature_id' => $feature->id,
                            'prompt_id' => $prompt->id,
                            'role' => $promptData['role'],
                            'priority' => $promptData['priority']
                        ]);
                        $this->command->info("  - Prompt bağlandı: {$prompt->name}");
                    }
                }
            }
        }
    }
}
