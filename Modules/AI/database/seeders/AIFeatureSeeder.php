<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
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
            ]
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
                'category' => 'content',
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
                'example_inputs' => [
                    ['text' => 'İstanbul\'da web tasarım hizmeti', 'label' => 'Yerel SEO'],
                    ['text' => 'E-ticaret sitesi kurma rehberi', 'label' => 'Rehber'],
                    ['text' => 'Dijital pazarlama trendleri 2024', 'label' => 'Trend']
                ],
                'example_prompts' => json_encode([
                    'Google\'da ilk sırada çıkmak istiyorum',
                    'Rakiplerimi geçecek içerik lazım',
                    'SEO uyumlu blog yazısı yaz'
                ]),
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            [
                'name' => 'Blog Yazısı Pro',
                'slug' => 'blog-writing-pro',
                'description' => 'Okuyucuların paylaşmak isteyeceği, Google\'ın seveceği blog yazıları.',
                'emoji' => '📝',
                'icon' => 'fas fa-blog',
                'category' => 'content',
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
                'example_inputs' => [
                    ['text' => 'Evden çalışma verimliliği artırma', 'label' => 'İş-Yaşam'],
                    ['text' => 'Kripto para yatırım rehberi', 'label' => 'Finans'],
                    ['text' => 'Sağlıklı yaşam için 10 altın kural', 'label' => 'Sağlık']
                ],
                'example_prompts' => json_encode([
                    'Viral olacak blog yazısı',
                    'LinkedIn\'de paylaşılacak içerik',
                    'Uzun ve detaylı rehber yazı'
                ]),
                'prompts' => [
                    ['name' => 'Blog Yazısı Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Girişimcilik üzerine thread', 'label' => 'Thread'],
                    ['text' => 'Motivasyon tweet\'i', 'label' => 'Tek Tweet'],
                    ['text' => 'Ürün lansmanı duyurusu', 'label' => 'Duyuru']
                ],
                'example_prompts' => json_encode([
                    'Viral olacak tweet yaz',
                    '10 tweet\'lik thread hazırla',
                    'Tartışma yaratacak görüş'
                ]),
                'prompts' => [
                    ['name' => 'Twitter İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Ürün tanıtım postu', 'label' => 'Ürün'],
                    ['text' => 'Motivasyon carousel\'i', 'label' => 'Carousel'],
                    ['text' => 'Reels video metni', 'label' => 'Reels']
                ],
                'example_prompts' => json_encode([
                    'Kaydet butonuna bastıracak post',
                    'Story\'de paylaşılacak içerik',
                    'Viral Reels senaryosu'
                ]),
                'prompts' => [
                    ['name' => 'Instagram İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // E-ticaret ve Satış
            [
                'name' => 'Ürün Açıklaması Pro',
                'slug' => 'product-description-pro',
                'description' => 'Satış yapan, ikna eden, sepete ekleten ürün açıklamaları.',
                'emoji' => '🛍️',
                'icon' => 'fas fa-shopping-cart',
                'category' => 'content',
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
                'example_inputs' => [
                    ['text' => 'Akıllı saat ürün açıklaması', 'label' => 'Teknoloji'],
                    ['text' => 'Organik bal satış metni', 'label' => 'Gıda'],
                    ['text' => 'Kadın çantası tanıtımı', 'label' => 'Moda']
                ],
                'example_prompts' => json_encode([
                    'Amazon için ürün açıklaması',
                    'Trendyol mağazam için metin',
                    'Satış odaklı ürün tanıtımı'
                ]),
                'prompts' => [
                    ['name' => 'Ürün Açıklama Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Video İçerik
            [
                'name' => 'YouTube SEO Master',
                'slug' => 'youtube-seo-master',
                'description' => 'İzlenme patlaması yapacak YouTube başlıkları ve açıklamaları.',
                'emoji' => '🎬',
                'icon' => 'fab fa-youtube',
                'category' => 'content',
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
                'example_inputs' => [
                    ['text' => 'Teknoloji inceleme videosu', 'label' => 'İnceleme'],
                    ['text' => 'Yemek tarifi videosu', 'label' => 'Yemek'],
                    ['text' => 'Eğitim içeriği videosu', 'label' => 'Eğitim']
                ],
                'example_prompts' => json_encode([
                    'Viral video başlığı',
                    'YouTube Shorts açıklaması',
                    'Video SEO optimizasyonu'
                ]),
                'prompts' => [
                    ['name' => 'YouTube SEO Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Hoşgeldin email serisi', 'label' => 'Onboarding'],
                    ['text' => 'Satış kampanyası emaili', 'label' => 'Satış'],
                    ['text' => 'Bülten içeriği', 'label' => 'Newsletter']
                ],
                'example_prompts' => json_encode([
                    'Açılma oranı yüksek konu satırı',
                    'Satış yapan email metni',
                    'Otomatik email serisi'
                ]),
                'prompts' => [
                    ['name' => 'Email Pazarlama Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'İstanbul diş kliniği', 'label' => 'Sağlık'],
                    ['text' => 'Ankara hukuk bürosu', 'label' => 'Hukuk'],
                    ['text' => 'İzmir restaurant', 'label' => 'Yeme-İçme']
                ],
                'example_prompts' => json_encode([
                    'Google My Business optimizasyonu',
                    'Yerel arama için içerik',
                    'Haritada üst sıralara çıkma'
                ]),
                'prompts' => [
                    ['name' => 'Yerel SEO Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Online kurs satış sayfası', 'label' => 'Eğitim'],
                    ['text' => 'SaaS ürünü landing page', 'label' => 'Yazılım'],
                    ['text' => 'Danışmanlık hizmeti sayfası', 'label' => 'Hizmet']
                ],
                'example_prompts' => json_encode([
                    'Yüksek dönüşümlü satış metni',
                    'Landing page başlıkları',
                    'CTA buton metinleri'
                ]),
                'prompts' => [
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi gizlilik politikası', 'label' => 'E-ticaret'],
                    ['text' => 'Mobil uygulama kullanım şartları', 'label' => 'Uygulama'],
                    ['text' => 'SaaS platformu veri işleme', 'label' => 'SaaS']
                ],
                'example_prompts' => json_encode([
                    'KVKK uyumlu gizlilik metni',
                    'Cookie politikası hazırla',
                    'Kullanıcı sözleşmesi yaz'
                ]),
                'prompts' => [
                    ['name' => 'Hukuki İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'REST API authentication', 'label' => 'Auth'],
                    ['text' => 'Payment gateway entegrasyonu', 'label' => 'Payment'],
                    ['text' => 'Webhook dokümantasyonu', 'label' => 'Webhook']
                ],
                'example_prompts' => json_encode([
                    'Swagger dokümantasyonu',
                    'API kullanım örnekleri',
                    'Error handling rehberi'
                ]),
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Startup kuruluş hikayesi', 'label' => 'Startup'],
                    ['text' => 'Aile şirketi mirası', 'label' => 'Aile İşi'],
                    ['text' => 'Sosyal girişim amacı', 'label' => 'Sosyal']
                ],
                'example_prompts' => json_encode([
                    'About Us sayfası metni',
                    'Kurucu hikayesi yazısı',
                    'Marka manifestosu'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Ürün schema markup', 'label' => 'Product'],
                    ['text' => 'Yerel işletme schema', 'label' => 'LocalBusiness'],
                    ['text' => 'FAQ schema markup', 'label' => 'FAQPage']
                ],
                'example_prompts' => json_encode([
                    'Zengin sonuçlar için schema',
                    'Google\'da yıldızlı görünüm',
                    'Breadcrumb schema kodu'
                ]),
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Hukuk bürosu ana sayfa', 'label' => 'Ana Sayfa'],
                    ['text' => 'E-ticaret kategori sayfası', 'label' => 'Kategori'],
                    ['text' => 'Blog yazısı meta tagları', 'label' => 'Blog']
                ],
                'example_prompts' => json_encode([
                    'Google\'da öne çıkan meta',
                    'Tıklama oranını artıran başlık',
                    'SEO uyumlu description'
                ]),
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // FAQ Generator
            [
                'name' => 'FAQ & SSS Üretici',
                'slug' => 'faq-generator',
                'description' => 'Müşteri sorularını önleyen kapsamlı SSS sayfaları.',
                'emoji' => '❓',
                'icon' => 'fas fa-question-circle',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 15,
                'badge_color' => 'warning',
                'input_placeholder' => 'İşletme türü ve sık sorulan konular...',
                'example_inputs' => [
                    ['text' => 'E-ticaret kargo ve iade', 'label' => 'E-ticaret'],
                    ['text' => 'SaaS fiyatlandırma soruları', 'label' => 'SaaS'],
                    ['text' => 'Hizmet süreci soruları', 'label' => 'Hizmet']
                ],
                'example_prompts' => json_encode([
                    'Müşteri desteği azaltan SSS',
                    'FAQ schema ile SSS',
                    'Satışı artıran sorular'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // WhatsApp Business
            [
                'name' => 'WhatsApp Business Pro',
                'slug' => 'whatsapp-business-pro',
                'description' => 'WhatsApp Business için otomatik mesajlar ve kampanyalar.',
                'emoji' => '💬',
                'icon' => 'fab fa-whatsapp',
                'category' => 'communication',
                'response_length' => 'short',
                'response_format' => 'text',
                'complexity_level' => 'beginner',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 16,
                'badge_color' => 'success',
                'input_placeholder' => 'Mesaj türü ve işletme bilgisi...',
                'example_inputs' => [
                    ['text' => 'Hoşgeldin mesajı', 'label' => 'Karşılama'],
                    ['text' => 'Katalog paylaşım mesajı', 'label' => 'Satış'],
                    ['text' => 'Sipariş durumu bildirimi', 'label' => 'Bildirim']
                ],
                'example_prompts' => json_encode([
                    'WhatsApp otomatik yanıt',
                    'Ürün tanıtım mesajı',
                    'Müşteri geri dönüş mesajı'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'B2B satış stratejileri', 'label' => 'Satış'],
                    ['text' => 'Liderlik ve yönetim', 'label' => 'Yönetim'],
                    ['text' => 'Dijital dönüşüm hikayeleri', 'label' => 'Teknoloji']
                ],
                'example_prompts' => json_encode([
                    'Viral LinkedIn postu',
                    'Thought leadership makalesi',
                    'Başarı hikayesi paylaşımı'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Komik iş hayatı videosu', 'label' => 'Komedi'],
                    ['text' => 'Hızlı tarif videosu', 'label' => 'Yemek'],
                    ['text' => 'Motivasyon içeriği', 'label' => 'Motivasyon']
                ],
                'example_prompts' => json_encode([
                    'Trend olan video fikri',
                    'TikTok challenge konsepti',
                    'Viral ses kullanım önerisi'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Press Release
            [
                'name' => 'Basın Bülteni Uzmanı',
                'slug' => 'press-release-expert',
                'description' => 'Medyanın ilgisini çekecek profesyonel basın bültenleri.',
                'emoji' => '📰',
                'icon' => 'fas fa-newspaper',
                'category' => 'communication',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 19,
                'badge_color' => 'info',
                'input_placeholder' => 'Haber değeri taşıyan konu ve detaylar...',
                'example_inputs' => [
                    ['text' => 'Yeni ürün lansmanı', 'label' => 'Lansman'],
                    ['text' => 'Şirket ortaklığı duyurusu', 'label' => 'Ortaklık'],
                    ['text' => 'Ödül ve başarı haberi', 'label' => 'Başarı']
                ],
                'example_prompts' => json_encode([
                    'Haber olacak basın metni',
                    'Medya ilgisi çekecek duyuru',
                    'PR ajansı kalitesinde bülten'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Case Study
            [
                'name' => 'Vaka Analizi Yazarı',
                'slug' => 'case-study-writer',
                'description' => 'Satışları artıran ikna edici başarı hikayeleri.',
                'emoji' => '📊',
                'icon' => 'fas fa-chart-line',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 20,
                'badge_color' => 'success',
                'input_placeholder' => 'Proje detayları ve sonuçlar...',
                'example_inputs' => [
                    ['text' => 'E-ticaret dönüşüm artışı', 'label' => 'E-ticaret'],
                    ['text' => 'B2B satış başarısı', 'label' => 'B2B'],
                    ['text' => 'Dijital dönüşüm projesi', 'label' => 'Dönüşüm']
                ],
                'example_prompts' => json_encode([
                    'Müşteri başarı hikayesi',
                    'ROI gösteren vaka analizi',
                    'Referans olacak proje özeti'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Girişimcilik röportajı', 'label' => 'Röportaj'],
                    ['text' => 'Teknoloji haberleri', 'label' => 'Haber'],
                    ['text' => 'Kişisel gelişim sohbeti', 'label' => 'Sohbet']
                ],
                'example_prompts' => json_encode([
                    'Podcast açılış metni',
                    'Konuk röportaj soruları',
                    'Bölüm sonu CTA'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
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
                'example_inputs' => [
                    ['text' => 'Ücretsiz deneme landing page', 'label' => 'SaaS'],
                    ['text' => 'Webinar kayıt sayfası', 'label' => 'Etkinlik'],
                    ['text' => 'E-book indirme sayfası', 'label' => 'Lead Gen']
                ],
                'example_prompts' => json_encode([
                    'A/B test için başlık varyasyonları',
                    'Form doldurma artıran copy',
                    'Güven unsurları metinleri'
                ]),
                'prompts' => [
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Newsletter Content
            [
                'name' => 'Newsletter İçerik Editörü',
                'slug' => 'newsletter-content-editor',
                'description' => 'Açılıp okunmayı garantileyen newsletter içerikleri.',
                'emoji' => '📮',
                'icon' => 'fas fa-mail-bulk',
                'category' => 'communication',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 23,
                'badge_color' => 'primary',
                'input_placeholder' => 'Newsletter konusu ve sıklığı...',
                'example_inputs' => [
                    ['text' => 'Haftalık teknoloji özeti', 'label' => 'Teknoloji'],
                    ['text' => 'Aylık şirket haberleri', 'label' => 'Kurumsal'],
                    ['text' => 'E-ticaret kampanya duyurusu', 'label' => 'Promosyon']
                ],
                'example_prompts' => json_encode([
                    'Yüksek açılma oranlı newsletter',
                    'Segmente özel içerik',
                    'Re-engagement kampanyası'
                ]),
                'prompts' => [
                    ['name' => 'Email Pazarlama Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Customer Service Templates
            [
                'name' => 'Müşteri Hizmetleri Sihirbazı',
                'slug' => 'customer-service-wizard',
                'description' => 'Müşteri memnuniyetini artıran hazır yanıt şablonları.',
                'emoji' => '🎧',
                'icon' => 'fas fa-headset',
                'category' => 'communication',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 24,
                'badge_color' => 'info',
                'input_placeholder' => 'Müşteri sorunu veya şikayet türü...',
                'example_inputs' => [
                    ['text' => 'Kargo gecikmesi şikayeti', 'label' => 'Kargo'],
                    ['text' => 'Ürün iade talebi', 'label' => 'İade'],
                    ['text' => 'Teknik destek isteği', 'label' => 'Destek']
                ],
                'example_prompts' => json_encode([
                    'Öfkeli müşteri yanıtı',
                    'İade prosedürü açıklaması',
                    'Özür ve çözüm metni'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Job Description Generator
            [
                'name' => 'İş İlanı Yaratıcısı',
                'slug' => 'job-description-creator',
                'description' => 'Yetenekleri çeken cazip iş ilanları ve pozisyon açıklamaları.',
                'emoji' => '👔',
                'icon' => 'fas fa-briefcase',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 25,
                'badge_color' => 'secondary',
                'input_placeholder' => 'Pozisyon adı ve departman bilgisi...',
                'example_inputs' => [
                    ['text' => 'Senior Frontend Developer', 'label' => 'Yazılım'],
                    ['text' => 'Dijital Pazarlama Uzmanı', 'label' => 'Pazarlama'],
                    ['text' => 'İnsan Kaynakları Müdürü', 'label' => 'İK']
                ],
                'example_prompts' => json_encode([
                    'Cazip iş ilanı metni',
                    'Görev tanımı listesi',
                    'Aranan nitelikler'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Voice Search Optimization
            [
                'name' => 'Sesli Arama Optimizasyonu',
                'slug' => 'voice-search-optimization',
                'description' => 'Alexa, Siri ve Google Assistant için optimize içerikler.',
                'emoji' => '🎤',
                'icon' => 'fas fa-microphone',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 26,
                'badge_color' => 'warning',
                'input_placeholder' => 'İşletme türü ve hizmetler...',
                'example_inputs' => [
                    ['text' => 'En yakın pizza restoranı', 'label' => 'Yerel'],
                    ['text' => 'Nasıl yapılır soruları', 'label' => 'How-to'],
                    ['text' => 'Ürün karşılaştırma', 'label' => 'Karşılaştırma']
                ],
                'example_prompts' => json_encode([
                    'Konuşma dilinde SEO',
                    'Soru cevap formatı',
                    'Yerel sesli arama'
                ]),
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

// Webinar Script
            [
                'name' => 'Webinar Senaryo Ustası',
                'slug' => 'webinar-script-master',
                'description' => 'Katılımcıları ekrana kilitleyen webinar senaryoları.',
                'emoji' => '🖥️',
                'icon' => 'fas fa-desktop',
                'category' => 'creative',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 27,
                'badge_color' => 'success',
                'input_placeholder' => 'Webinar konusu ve hedef kitle...',
                'example_inputs' => [
                    ['text' => 'B2B satış teknikleri', 'label' => 'Satış'],
                    ['text' => 'Dijital pazarlama 101', 'label' => 'Eğitim'],
                    ['text' => 'Ürün demo webinarı', 'label' => 'Demo']
                ],
                'example_prompts' => json_encode([
                    'Webinar açılış konuşması',
                    'İnteraktif anket soruları',
                    'CTA ve kapanış'
                ]),
                'prompts' => [
                    ['name' => 'Yaratıcı İçerik Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Chatbot Scripts
            [
                'name' => 'Chatbot Diyalog Tasarımcısı',
                'slug' => 'chatbot-dialog-designer',
                'description' => 'Müşteri deneyimini artıran akıllı chatbot diyalogları.',
                'emoji' => '🤖',
                'icon' => 'fas fa-robot',
                'category' => 'technical',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 28,
                'badge_color' => 'dark',
                'input_placeholder' => 'Chatbot amacı ve senaryolar...',
                'example_inputs' => [
                    ['text' => 'Müşteri destek chatbotu', 'label' => 'Destek'],
                    ['text' => 'Satış asistanı bot', 'label' => 'Satış'],
                    ['text' => 'Randevu alma botu', 'label' => 'Randevu']
                ],
                'example_prompts' => json_encode([
                    'Karşılama mesajları',
                    'Sık sorulan sorular akışı',
                    'Hata yönetimi diyalogları'
                ]),
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Google Ads Copy
            [
                'name' => 'Google Ads Uzmanı',
                'slug' => 'google-ads-expert',
                'description' => 'Yüksek kalite skoru alan Google Ads metinleri.',
                'emoji' => '🎯',
                'icon' => 'fab fa-google',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 29,
                'badge_color' => 'primary',
                'input_placeholder' => 'Ürün/hizmet ve hedef kitle...',
                'example_inputs' => [
                    ['text' => 'Avukat Google reklamı', 'label' => 'Hukuk'],
                    ['text' => 'E-ticaret kampanyası', 'label' => 'E-ticaret'],
                    ['text' => 'Yerel hizmet reklamı', 'label' => 'Yerel']
                ],
                'example_prompts' => json_encode([
                    'Responsive search ads',
                    'Call-only kampanya',
                    'Shopping ads metni'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Amazon Product Listing
            [
                'name' => 'Amazon Satış Ustası',
                'slug' => 'amazon-sales-master',
                'description' => 'Amazon\'da Best Seller yapacak ürün listeleme metinleri.',
                'emoji' => '📦',
                'icon' => 'fab fa-amazon',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 30,
                'badge_color' => 'warning',
                'input_placeholder' => 'Ürün detayları ve kategori...',
                'example_inputs' => [
                    ['text' => 'Elektronik aksesuar', 'label' => 'Elektronik'],
                    ['text' => 'Ev ve yaşam ürünü', 'label' => 'Ev'],
                    ['text' => 'Spor ve outdoor', 'label' => 'Spor']
                ],
                'example_prompts' => json_encode([
                    'A9 algoritması için başlık',
                    'Bullet points yazımı',
                    'Backend anahtar kelimeler'
                ]),
                'prompts' => [
                    ['name' => 'Ürün Açıklama Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Internal Communication
            [
                'name' => 'Kurumsal İletişim Uzmanı',
                'slug' => 'internal-communication-expert',
                'description' => 'Çalışan motivasyonunu artıran kurumsal iletişim metinleri.',
                'emoji' => '🏢',
                'icon' => 'fas fa-building',
                'category' => 'communication',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 31,
                'badge_color' => 'secondary',
                'input_placeholder' => 'Mesaj türü ve konu...',
                'example_inputs' => [
                    ['text' => 'CEO yıl sonu mesajı', 'label' => 'Liderlik'],
                    ['text' => 'Değişim duyurusu', 'label' => 'Duyuru'],
                    ['text' => 'Başarı kutlama maili', 'label' => 'Kutlama']
                ],
                'example_prompts' => json_encode([
                    'All-hands meeting duyurusu',
                    'Şirket politika güncellemesi',
                    'Takım motivasyon mesajı'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Grant Proposal
            [
                'name' => 'Hibe Başvuru Uzmanı',
                'slug' => 'grant-proposal-expert',
                'description' => 'Fonlama şansını artıran ikna edici hibe başvuruları.',
                'emoji' => '💎',
                'icon' => 'fas fa-gem',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 32,
                'badge_color' => 'primary',
                'input_placeholder' => 'Proje detayları ve hibe programı...',
                'example_inputs' => [
                    ['text' => 'KOSGEB teknoloji desteği', 'label' => 'KOSGEB'],
                    ['text' => 'TÜBİTAK araştırma projesi', 'label' => 'TÜBİTAK'],
                    ['text' => 'AB hibeleri başvurusu', 'label' => 'AB']
                ],
                'example_prompts' => json_encode([
                    'Proje özeti yazımı',
                    'Bütçe gerekçelendirmesi',
                    'Etki analizi metni'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Influencer Outreach
            [
                'name' => 'Influencer İletişim Uzmanı',
                'slug' => 'influencer-outreach-expert',
                'description' => 'Influencer\'ları ikna eden işbirliği teklifleri.',
                'emoji' => '⭐',
                'icon' => 'fas fa-star',
                'category' => 'marketing',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 33,
                'badge_color' => 'warning',
                'input_placeholder' => 'Marka ve influencer profili...',
                'example_inputs' => [
                    ['text' => 'Moda markası işbirliği', 'label' => 'Moda'],
                    ['text' => 'Teknoloji ürün tanıtımı', 'label' => 'Teknoloji'],
                    ['text' => 'Yemek markası kampanyası', 'label' => 'Yemek']
                ],
                'example_prompts' => json_encode([
                    'İlk iletişim mesajı',
                    'İşbirliği teklifi',
                    'Kampanya brief\'i'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Crisis Communication
            [
                'name' => 'Kriz İletişimi Uzmanı',
                'slug' => 'crisis-communication-expert',
                'description' => 'Kriz anlarında güveni koruyan iletişim stratejileri.',
                'emoji' => '🚨',
                'icon' => 'fas fa-exclamation-triangle',
                'category' => 'communication',
                'response_length' => 'medium',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 34,
                'badge_color' => 'danger',
                'input_placeholder' => 'Kriz türü ve detayları...',
                'example_inputs' => [
                    ['text' => 'Ürün geri çağırma', 'label' => 'Ürün'],
                    ['text' => 'Veri ihlali açıklaması', 'label' => 'Güvenlik'],
                    ['text' => 'Olumsuz basın haberi', 'label' => 'PR']
                ],
                'example_prompts' => json_encode([
                    'Özür metni hazırla',
                    'Basın açıklaması',
                    'Sosyal medya yanıtı'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Event Promotion
            [
                'name' => 'Etkinlik Tanıtım Uzmanı',
                'slug' => 'event-promotion-expert',
                'description' => 'Katılımcı sayısını artıran etkinlik tanıtım kampanyaları.',
                'emoji' => '🎉',
                'icon' => 'fas fa-calendar-alt',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 35,
                'badge_color' => 'info',
                'input_placeholder' => 'Etkinlik türü ve detayları...',
                'example_inputs' => [
                    ['text' => 'Online konferans', 'label' => 'Konferans'],
                    ['text' => 'Ürün lansmanı etkinliği', 'label' => 'Lansman'],
                    ['text' => 'Workshop ve eğitim', 'label' => 'Eğitim']
                ],
                'example_prompts' => json_encode([
                    'Early bird kampanyası',
                    'Sosyal medya duyuruları',
                    'Email davetiye metni'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Partnership Proposal
            [
                'name' => 'İş Ortaklığı Teklifi Uzmanı',
                'slug' => 'partnership-proposal-expert',
                'description' => 'Win-win ortaklıklar kuran ikna edici teklifler.',
                'emoji' => '🤝',
                'icon' => 'fas fa-handshake',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 36,
                'badge_color' => 'success',
                'input_placeholder' => 'Ortaklık türü ve taraflar...',
                'example_inputs' => [
                    ['text' => 'Teknoloji entegrasyonu', 'label' => 'Teknoloji'],
                    ['text' => 'Co-marketing anlaşması', 'label' => 'Pazarlama'],
                    ['text' => 'Dağıtım ortaklığı', 'label' => 'Dağıtım']
                ],
                'example_prompts' => json_encode([
                    'İlk temas mektubu',
                    'Ortaklık value proposition',
                    'Win-win model önerisi'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Investor Pitch
            [
                'name' => 'Yatırımcı Sunumu Uzmanı',
                'slug' => 'investor-pitch-expert',
                'description' => 'Yatırım çeken güçlü pitch deck metinleri.',
                'emoji' => '💸',
                'icon' => 'fas fa-chart-line',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 37,
                'badge_color' => 'primary',
                'input_placeholder' => 'Startup ve yatırım turu bilgileri...',
                'example_inputs' => [
                    ['text' => 'Seed yatırım turu', 'label' => 'Seed'],
                    ['text' => 'Series A pitch', 'label' => 'Series A'],
                    ['text' => 'Melek yatırımcı sunumu', 'label' => 'Angel']
                ],
                'example_prompts' => json_encode([
                    'Elevator pitch metni',
                    'Problem-solution slide',
                    'Traction ve metrikler'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Onboarding Content
            [
                'name' => 'Kullanıcı Onboarding Uzmanı',
                'slug' => 'user-onboarding-expert',
                'description' => 'Kullanıcı aktivasyonunu artıran onboarding içerikleri.',
                'emoji' => '🚀',
                'icon' => 'fas fa-rocket',
                'category' => 'technical',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 38,
                'badge_color' => 'warning',
                'input_placeholder' => 'Ürün türü ve kullanıcı profili...',
                'example_inputs' => [
                    ['text' => 'SaaS onboarding', 'label' => 'SaaS'],
                    ['text' => 'Mobil app tutorial', 'label' => 'Mobil'],
                    ['text' => 'E-ticaret ilk alışveriş', 'label' => 'E-ticaret']
                ],
                'example_prompts' => json_encode([
                    'Welcome email serisi',
                    'İlk kullanım rehberi',
                    'Tooltip ve guide metinleri'
                ]),
                'prompts' => [
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Affiliate Marketing
            [
                'name' => 'Affiliate Pazarlama Uzmanı',
                'slug' => 'affiliate-marketing-expert',
                'description' => 'Yüksek komisyon kazandıran affiliate içerikler.',
                'emoji' => '🔗',
                'icon' => 'fas fa-link',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 39,
                'badge_color' => 'success',
                'input_placeholder' => 'Ürün kategorisi ve affiliate programı...',
                'example_inputs' => [
                    ['text' => 'Teknoloji ürün incelemesi', 'label' => 'Teknoloji'],
                    ['text' => 'Finans araçları karşılaştırma', 'label' => 'Finans'],
                    ['text' => 'Seyahat rezervasyon önerileri', 'label' => 'Seyahat']
                ],
                'example_prompts' => json_encode([
                    'Karşılaştırma tablosu metni',
                    'Satın alma rehberi',
                    'Ürün önerisi makalesi'
                ]),
                'prompts' => [
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // Exit Intent Popup
            [
                'name' => 'Exit Intent Popup Uzmanı',
                'slug' => 'exit-intent-popup-expert',
                'description' => 'Ziyaretçileri geri kazanan exit popup metinleri.',
                'emoji' => '🛑',
                'icon' => 'fas fa-door-open',
                'category' => 'marketing',
                'response_length' => 'short',
                'response_format' => 'structured',
                'complexity_level' => 'intermediate',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 40,
                'badge_color' => 'danger',
                'input_placeholder' => 'Teklif türü ve hedef...',
                'example_inputs' => [
                    ['text' => 'İndirim kuponu teklifi', 'label' => 'İndirim'],
                    ['text' => 'Newsletter kaydı', 'label' => 'Email'],
                    ['text' => 'Ücretsiz kargo teklifi', 'label' => 'Kargo']
                ],
                'example_prompts' => json_encode([
                    'Son şans teklifi',
                    'Email karşılığı hediye',
                    'Sepet bırakma önleme'
                ]),
                'prompts' => [
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'primary', 'priority' => 1]
                ]
            ],

            // YENİ ÖZELLİKLER - Advanced Multi-Prompt Features

            // Kapsamlı SEO Analizi - Multiple Experts Combined
            [
                'name' => 'Kapsamlı SEO Analizi',
                'slug' => 'comprehensive-seo-analysis',
                'description' => 'Teknik, içerik ve sosyal medya SEO\'sunu birleştiren tam analiz.',
                'emoji' => '🔍',
                'icon' => 'fas fa-search-plus',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 41,
                'badge_color' => 'primary',
                'input_placeholder' => 'Website URL\'si ve hedef anahtar kelimeler...',
                'example_inputs' => [
                    ['text' => 'E-ticaret sitesi SEO analizi', 'label' => 'E-ticaret'],
                    ['text' => 'Blog sitesi optimizasyonu', 'label' => 'Blog'],
                    ['text' => 'Kurumsal web sitesi analizi', 'label' => 'Kurumsal']
                ],
                'example_prompts' => json_encode([
                    'Tam SEO denetimi yap',
                    'Rakip analizi ile karşılaştırma',
                    'Aksiyon planı çıkar'
                ]),
                'prompts' => [
                    ['name' => 'SEO İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'GA4 & Search Console Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Teknik Dokümantasyon Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // AI-Powered İçerik Kampanyası
            [
                'name' => 'AI-Powered İçerik Kampanyası',
                'slug' => 'ai-powered-content-campaign',
                'description' => 'AI araçları kullanarak çok platformlu içerik kampanyası tasarlar.',
                'emoji' => '🤖',
                'icon' => 'fas fa-robot',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 42,
                'badge_color' => 'warning',
                'input_placeholder' => 'Kampanya hedefi ve bütçe bilgisi...',
                'example_inputs' => [
                    ['text' => 'Ürün lansmanı kampanyası', 'label' => 'Lansman'],
                    ['text' => 'Marka bilinirliği artırma', 'label' => 'Branding'],
                    ['text' => 'Lead generation kampanyası', 'label' => 'Lead Gen']
                ],
                'example_prompts' => json_encode([
                    'Otomatik içerik üretimi',
                    'Çok kanallı kampanya',
                    'Performance tracking sistemi'
                ]),
                'prompts' => [
                    ['name' => 'AI Otomasyon Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'GA4 & Search Console Uzmanı', 'role' => 'secondary', 'priority' => 2]
                ]
            ],

            // Global Pazar Girişi
            [
                'name' => 'Global Pazar Girişi',
                'slug' => 'global-market-entry',
                'description' => 'Uluslararası pazarlara giriş için çok dilli SEO stratejisi.',
                'emoji' => '🌍',
                'icon' => 'fas fa-globe',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 43,
                'badge_color' => 'info',
                'input_placeholder' => 'Hedef ülke/dil ve sektör bilgisi...',
                'example_inputs' => [
                    ['text' => 'Almanya pazarına giriş', 'label' => 'Almanya'],
                    ['text' => 'İngilizce pazarlarda büyüme', 'label' => 'İngilizce'],
                    ['text' => 'Arap ülkeleri stratejisi', 'label' => 'MENA']
                ],
                'example_prompts' => json_encode([
                    'Çoklu dil SEO stratejisi',
                    'Kültürel adaptasyon rehberi',
                    'Yerel rakip analizi'
                ]),
                'prompts' => [
                    ['name' => 'Çoklu Dil SEO Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Yerel SEO Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // Video Marketing Ekosisteemi
            [
                'name' => 'Video Marketing Ekosisteemi',
                'slug' => 'video-marketing-ecosystem',
                'description' => 'Tüm platformlar için entegre video pazarlama sistemi.',
                'emoji' => '🎬',
                'icon' => 'fas fa-video',
                'category' => 'content',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 44,
                'badge_color' => 'danger',
                'input_placeholder' => 'Video türü ve hedef platform...',
                'example_inputs' => [
                    ['text' => 'Ürün tanıtım video serisi', 'label' => 'Ürün'],
                    ['text' => 'Eğitim içerikli videolar', 'label' => 'Eğitim'],
                    ['text' => 'Marka hikayesi videoları', 'label' => 'Branding']
                ],
                'example_prompts' => json_encode([
                    'Çok platformlu video stratejisi',
                    'Video SEO optimizasyonu',
                    'Engagement artırma teknikleri'
                ]),
                'prompts' => [
                    ['name' => 'Video İçerik Stratejisti', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'YouTube SEO Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // Fintech Tam Paketi
            [
                'name' => 'Fintech İçerik Tam Paketi',
                'slug' => 'fintech-complete-package',
                'description' => 'Fintech şirketleri için yasal uyumlu pazarlama ekosistemi.',
                'emoji' => '💰',
                'icon' => 'fas fa-coins',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 45,
                'badge_color' => 'success',
                'input_placeholder' => 'Fintech ürün türü ve hedef kitle...',
                'example_inputs' => [
                    ['text' => 'Kripto exchange platformu', 'label' => 'Exchange'],
                    ['text' => 'P2P ödeme uygulaması', 'label' => 'Payment'],
                    ['text' => 'Robo-advisor hizmeti', 'label' => 'Investment']
                ],
                'example_prompts' => json_encode([
                    'SPK uyumlu içerik stratejisi',
                    'Güven odaklı pazarlama',
                    'Eğitsel içerik planı'
                ]),
                'prompts' => [
                    ['name' => 'Fintech & Kripto İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Hukuki İçerik Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'secondary', 'priority' => 2]
                ]
            ],

            // E-ticaret Büyüme Paketi
            [
                'name' => 'E-ticaret Büyüme Paketi',
                'slug' => 'ecommerce-growth-package',
                'description' => 'E-ticaret sitelerini büyütecek entegre pazarlama çözümü.',
                'emoji' => '🛒',
                'icon' => 'fas fa-shopping-cart',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 46,
                'badge_color' => 'warning',
                'input_placeholder' => 'E-ticaret kategorisi ve satış hedefi...',
                'example_inputs' => [
                    ['text' => 'Moda e-ticaret sitesi', 'label' => 'Moda'],
                    ['text' => 'Elektronik ürünler', 'label' => 'Elektronik'],
                    ['text' => 'Ev ve yaşam ürünleri', 'label' => 'Ev & Yaşam']
                ],
                'example_prompts' => json_encode([
                    'Satış artırıcı ürün açıklamaları',
                    'Amazon SEO optimizasyonu',
                    'Sosyal ticaret stratejisi'
                ]),
                'prompts' => [
                    ['name' => 'Ürün Açıklama Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Amazon Satış Ustası', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Dönüşüm Optimizasyon Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Instagram İçerik Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // Startup Tam Destek
            [
                'name' => 'Startup Tam Destek Paketi',
                'slug' => 'startup-complete-support',
                'description' => 'Startup\'lar için sıfırdan pazarlama ekosistemi kurulumu.',
                'emoji' => '🚀',
                'icon' => 'fas fa-rocket',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 47,
                'badge_color' => 'primary',
                'input_placeholder' => 'Startup sektörü ve yatırım aşaması...',
                'example_inputs' => [
                    ['text' => 'SaaS startup pre-seed', 'label' => 'SaaS'],
                    ['text' => 'E-ticaret Series A', 'label' => 'E-ticaret'],
                    ['text' => 'Fintech seed aşama', 'label' => 'Fintech']
                ],
                'example_prompts' => json_encode([
                    'Sıfırdan marka hikayesi',
                    'Yatırımcı pitch materyali',
                    'Go-to-market stratejisi'
                ]),
                'prompts' => [
                    ['name' => 'Marka Hikayesi Yaratıcısı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Yatırımcı Sunumu Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'İş Ortaklığı Teklifi Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Landing Page Mimarı', 'role' => 'secondary', 'priority' => 2]
                ]
            ],

            // Influencer Ekosistemi
            [
                'name' => 'Influencer Marketing Ekosistemi',
                'slug' => 'influencer-marketing-ecosystem',
                'description' => 'Influencer pazarlaması için tam süreç yönetimi.',
                'emoji' => '⭐',
                'icon' => 'fas fa-star',
                'category' => 'marketing',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'advanced',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 48,
                'badge_color' => 'warning',
                'input_placeholder' => 'Kampanya türü ve hedef influencer profili...',
                'example_inputs' => [
                    ['text' => 'Mikro-influencer kampanyası', 'label' => 'Mikro'],
                    ['text' => 'Mega influencer işbirliği', 'label' => 'Mega'],
                    ['text' => 'Nano influencer ağı', 'label' => 'Nano']
                ],
                'example_prompts' => json_encode([
                    'Influencer keşif stratejisi',
                    'İşbirliği teklif paketi',
                    'ROI ölçüm sistemi'
                ]),
                'prompts' => [
                    ['name' => 'Influencer İletişim Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Instagram İçerik Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'TikTok Viral Factory', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'İçerik Üretim Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // Kriz Yönetimi Tam Paketi
            [
                'name' => 'Kriz Yönetimi & İtibar Koruma',
                'slug' => 'crisis-management-reputation',
                'description' => 'Kriz anlarında itibarı koruyan kapsamlı iletişim paketi.',
                'emoji' => '🛡️',
                'icon' => 'fas fa-shield-alt',
                'category' => 'communication',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'show_in_examples' => true,
                'sort_order' => 49,
                'badge_color' => 'danger',
                'input_placeholder' => 'Kriz türü ve etki alanı...',
                'example_inputs' => [
                    ['text' => 'Ürün hatası krizi', 'label' => 'Ürün'],
                    ['text' => 'Veri güvenliği ihlali', 'label' => 'Güvenlik'],
                    ['text' => 'Sosyal medya krizi', 'label' => 'Sosyal Medya']
                ],
                'example_prompts' => json_encode([
                    'Hızlı müdahale planı',
                    'İtibar onarım stratejisi',
                    'Medya ilişkileri yönetimi'
                ]),
                'prompts' => [
                    ['name' => 'Kriz İletişimi Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Basın Bülteni Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Kurumsal İletişim Uzmanı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Hukuki İçerik Uzmanı', 'role' => 'support', 'priority' => 3]
                ]
            ],

            // B2B Satış Makinesi
            [
                'name' => 'B2B Satış Makinesi',
                'slug' => 'b2b-sales-machine',
                'description' => 'B2B satışları patlatan entegre pazarlama automasyonu.',
                'emoji' => '💼',
                'icon' => 'fas fa-briefcase',
                'category' => 'business',
                'response_length' => 'long',
                'response_format' => 'structured',
                'complexity_level' => 'expert',
                'status' => 'active',
                'is_system' => true,
                'is_featured' => true,
                'show_in_examples' => true,
                'sort_order' => 50,
                'badge_color' => 'primary',
                'input_placeholder' => 'B2B hizmet türü ve hedef sektör...',
                'example_inputs' => [
                    ['text' => 'SaaS ürünü B2B satışı', 'label' => 'SaaS'],
                    ['text' => 'Danışmanlık hizmeti', 'label' => 'Consultancy'],
                    ['text' => 'B2B e-ticaret platformu', 'label' => 'Platform']
                ],
                'example_prompts' => json_encode([
                    'LinkedIn odaklı satış hunisi',
                    'Email nurturing sekvansı',
                    'Thought leadership içerikleri'
                ]),
                'prompts' => [
                    ['name' => 'LinkedIn Thought Leader', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Email Pazarlama Uzmanı', 'role' => 'primary', 'priority' => 1],
                    ['name' => 'Landing Page Mimarı', 'role' => 'secondary', 'priority' => 2],
                    ['name' => 'Vaka Analizi Yazarı', 'role' => 'support', 'priority' => 3]
                ]
            ]
        ];

        foreach ($features as $featureData) {
            // Prompt bilgilerini ayır
            $prompts = $featureData['prompts'];
            unset($featureData['prompts']);

            // Feature oluştur veya güncelle
            $feature = AIFeature::updateOrCreate(
                ['slug' => $featureData['slug']],
                $featureData
            );

            // Mevcut prompt bağlantılarını temizle
            $feature->featurePrompts()->delete();

            // Prompt'ları bağla
            foreach ($prompts as $promptData) {
                $prompt = Prompt::where('name', $promptData['name'])->first();
                if ($prompt) {
                    AIFeaturePrompt::create([
                        'ai_feature_id' => $feature->id,
                        'ai_prompt_id' => $prompt->id,
                        'prompt_role' => $promptData['role'],
                        'priority' => $promptData['priority'],
                        'is_required' => true,
                        'is_active' => true
                    ]);
                }
            }
        }
    }
}