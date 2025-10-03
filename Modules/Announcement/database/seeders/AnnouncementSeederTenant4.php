<?php

namespace Modules\Announcement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Announcement\App\Models\Announcement;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Announcement Seeder for Tenant4
 * Languages: en (only)
 */
class AnnouncementSeederTenant4 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT4 pages (en only)...');

        // Duplicate kontrolü
        $existingCount = Announcement::count();
        if ($existingCount > 0) {
            $this->command->info("Pages already exist in TENANT4 database ({$existingCount} pages), skipping seeder...");
            return;
        }

        // Mevcut sayfaları sil (sadece boşsa)
        Announcement::truncate();


        $this->createHomepage();
        $this->createAboutPage();
        $this->createProductsPage();
        $this->createSupportPage();
        $this->createContactPage();
        $this->createPricingPage();
    }

    private function createHomepage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'SaaS Platform - Streamline Your Business'
            ],
            'slug' => [
                'en' => 'homepage'
            ],
            'body' => [
                'en' => '<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50">
                    <div class="container mx-auto px-4 py-8">
                        <div class="text-center mb-8">
                            <h1 class="text-6xl font-bold text-gray-900 mb-4">
                                <span class="bg-gradient-to-r from-indigo-600 to-cyan-600 bg-clip-text text-transparent">Streamline</span><br>
                                <span class="text-gray-800">Your Business Operations</span>
                            </h1>
                            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                                All-in-one SaaS platform designed to automate workflows, manage teams, and accelerate growth for businesses of all sizes.
                            </p>
                            <div class="flex gap-6 justify-center">
                                <button class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-10 py-4 rounded-full text-lg font-semibold hover:shadow-xl transform hover:-translate-y-1 transition-all">
                                    Start Free Trial
                                </button>
                                <button class="border-2 border-indigo-600 text-indigo-600 px-10 py-4 rounded-full text-lg font-semibold hover:bg-indigo-50 transition-all">
                                    Watch Demo
                                </button>
                            </div>
                        </div>

                        <!-- Feature Highlights -->
                        <div class="grid md:grid-cols-3 gap-8 mt-16">
                            <div class="text-center p-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl">⚡</span>
                                </div>
                                <h3 class="text-xl font-bold mb-3">Lightning Fast</h3>
                                <p class="text-gray-600">Built for speed with modern architecture and optimized performance.</p>
                            </div>
                            <div class="text-center p-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl">🔒</span>
                                </div>
                                <h3 class="text-xl font-bold mb-3">Enterprise Security</h3>
                                <p class="text-gray-600">Bank-level security with SOC 2 Type II compliance and data encryption.</p>
                            </div>
                            <div class="text-center p-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl">📈</span>
                                </div>
                                <h3 class="text-xl font-bold mb-3">Scalable Growth</h3>
                                <p class="text-gray-600">Grows with your business from startup to enterprise scale.</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'SaaS Platform - Streamline Your Business Operations',
            'All-in-one SaaS platform designed to automate workflows, manage teams, and accelerate growth for businesses of all sizes.'
        );
    }

    private function createAboutPage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'About Us'
            ],
            'slug' => [
                'en' => 'about-us'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="max-w-6xl mx-auto">
                        <div class="text-center mb-16">
                            <h1 class="text-5xl font-bold text-gray-800 mb-6">About Our Company</h1>
                            <p class="text-xl text-gray-600 max-w-4xl mx-auto">
                                Founded in 2018, we are dedicated to creating powerful, user-friendly software solutions that help businesses operate more efficiently and grow faster in the digital age.
                            </p>
                        </div>

                        <!-- Stats -->
                        <div class="grid md:grid-cols-4 gap-8 mb-16">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-indigo-600 mb-2">250K+</div>
                                <p class="text-gray-600">Active Users</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-indigo-600 mb-2">99.9%</div>
                                <p class="text-gray-600">Uptime</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-indigo-600 mb-2">150+</div>
                                <p class="text-gray-600">Countries</p>
                            </div>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-indigo-600 mb-2">24/7</div>
                                <p class="text-gray-600">Support</p>
                            </div>
                        </div>

                        <!-- Mission & Vision -->
                        <div class="grid md:grid-cols-2 gap-16 items-center">
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-8">
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Mission</h2>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    To empower businesses worldwide with intelligent software solutions that simplify complex operations, enhance productivity, and drive sustainable growth through innovative technology.
                                </p>
                            </div>
                            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8">
                                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Vision</h2>
                                <p class="text-gray-700 text-lg leading-relaxed">
                                    To become the global leader in business automation software, creating a world where every organization can achieve their full potential through seamless technology integration.
                                </p>
                            </div>
                        </div>

                        <!-- Values -->
                        <div class="mt-16">
                            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Our Core Values</h2>
                            <div class="grid md:grid-cols-3 gap-8">
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">💡</span>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Innovation</h3>
                                    <p class="text-gray-600">Constantly pushing boundaries to create cutting-edge solutions.</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">🤝</span>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Collaboration</h3>
                                    <p class="text-gray-600">Building strong partnerships with our customers and team.</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span class="text-3xl">🎯</span>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Excellence</h3>
                                    <p class="text-gray-600">Delivering exceptional quality in everything we do.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'About Us - Leading SaaS Solutions Provider',
            'Founded in 2018, we are dedicated to creating powerful, user-friendly software solutions that help businesses operate more efficiently and grow faster.'
        );
    }

    private function createProductsPage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Products & Features'
            ],
            'slug' => [
                'en' => 'products'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Products & Features</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Comprehensive business tools designed to streamline operations and accelerate growth.
                        </p>
                    </div>

                    <!-- Main Products -->
                    <div class="grid lg:grid-cols-2 gap-12 mb-16">
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-xl text-white">📊</span>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800">Business Analytics</h2>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Real-time insights and detailed reporting to make data-driven decisions with confidence.
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Interactive Dashboards</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Custom Reports</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Predictive Analytics</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Data Export & API</li>
                            </ul>
                        </div>

                        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-3xl p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center mr-4">
                                    <span class="text-xl text-white">⚙️</span>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-800">Workflow Automation</h2>
                            </div>
                            <p class="text-gray-600 mb-6">
                                Automate repetitive tasks and create sophisticated workflows without coding.
                            </p>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Drag & Drop Builder</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> 500+ Integrations</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Conditional Logic</li>
                                <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Scheduled Actions</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Feature Grid -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">👥</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Team Management</h3>
                            <p class="text-gray-600">Organize teams, assign roles, and track performance with comprehensive management tools.</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">📱</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Mobile Apps</h3>
                            <p class="text-gray-600">Native iOS and Android apps with offline capability and real-time sync.</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">🔐</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Advanced Security</h3>
                            <p class="text-gray-600">Enterprise-grade security with SSO, 2FA, and compliance certifications.</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">🔗</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">API & Integrations</h3>
                            <p class="text-gray-600">Connect with 500+ popular business tools and build custom integrations.</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">💬</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Live Chat Support</h3>
                            <p class="text-gray-600">24/7 customer support with live chat, video calls, and priority assistance.</p>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-xl text-white">⚡</span>
                            </div>
                            <h3 class="text-xl font-bold mb-3">Performance</h3>
                            <p class="text-gray-600">Lightning-fast performance with 99.9% uptime guarantee and global CDN.</p>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Products & Features - Business Analytics & Workflow Automation',
            'Comprehensive business tools including analytics, workflow automation, team management, and enterprise security features.'
        );
    }

    private function createSupportPage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Help & Support'
            ],
            'slug' => [
                'en' => 'support'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Help & Support</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Get the help you need to make the most of our platform with comprehensive resources and 24/7 support.
                        </p>
                    </div>

                    <!-- Support Options -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                        <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow border border-gray-100">
                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl text-white">💬</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4">Live Chat</h3>
                            <p class="text-gray-600 mb-6">Get instant answers from our support team available 24/7.</p>
                            <button class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Start Chat
                            </button>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow border border-gray-100">
                            <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl text-white">📧</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4">Email Support</h3>
                            <p class="text-gray-600 mb-6">Send us detailed questions and get comprehensive responses.</p>
                            <button class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Send Email
                            </button>
                        </div>

                        <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow border border-gray-100">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-2xl text-white">📱</span>
                            </div>
                            <h3 class="text-xl font-bold mb-4">Phone Support</h3>
                            <p class="text-gray-600 mb-6">Speak directly with our experts for complex technical issues.</p>
                            <button class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Schedule Call
                            </button>
                        </div>
                    </div>

                    <!-- Quick Help -->
                    <div class="grid md:grid-cols-2 gap-12">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-8">Frequently Asked Questions</h2>
                            <div class="space-y-6">
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">How do I get started with the platform?</h4>
                                    <p class="text-gray-600">Sign up for a free trial, follow our quick setup guide, and start with our pre-built templates.</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">Can I integrate with other tools?</h4>
                                    <p class="text-gray-600">Yes, we support 500+ integrations including popular tools like Slack, Google Workspace, and Salesforce.</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">Is my data secure?</h4>
                                    <p class="text-gray-600">Absolutely. We use enterprise-grade security with encryption, SOC 2 compliance, and regular security audits.</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="font-semibold text-gray-800 mb-2">What if I need to cancel my subscription?</h4>
                                    <p class="text-gray-600">You can cancel anytime from your account settings. No long-term contracts or cancellation fees.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-8">Learning Resources</h2>
                            <div class="space-y-6">
                                <div class="flex items-start bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-4">
                                        <span class="text-xl text-white">📚</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Documentation</h4>
                                        <p class="text-gray-600">Complete guides, API references, and tutorials for all features.</p>
                                    </div>
                                </div>

                                <div class="flex items-start bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center mr-4">
                                        <span class="text-xl text-white">🎥</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Video Tutorials</h4>
                                        <p class="text-gray-600">Step-by-step video guides for common tasks and advanced features.</p>
                                    </div>
                                </div>

                                <div class="flex items-start bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-4">
                                        <span class="text-xl text-white">🎓</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Training Courses</h4>
                                        <p class="text-gray-600">Interactive courses to master our platform and boost productivity.</p>
                                    </div>
                                </div>

                                <div class="flex items-start bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-lg flex items-center justify-center mr-4">
                                        <span class="text-xl text-white">🌐</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Community Forum</h4>
                                        <p class="text-gray-600">Connect with other users, share tips, and get community support.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Help & Support - 24/7 Customer Service & Resources',
            'Get comprehensive support with live chat, email, phone support, and extensive learning resources including documentation and tutorials.'
        );
    }

    private function createContactPage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Contact Sales'
            ],
            'slug' => [
                'en' => 'contact'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Contact Our Sales Team</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Ready to transform your business? Get in touch with our sales experts to find the perfect plan for your needs.
                        </p>
                    </div>

                    <div class="max-w-6xl mx-auto">
                        <div class="grid lg:grid-cols-2 gap-12">
                            <!-- Contact Form -->
                            <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Get Started Today</h2>
                                <p class="text-gray-600 mb-8">
                                    Fill out the form below and our sales team will contact you within 24 hours to discuss your requirements.
                                </p>

                                <div class="space-y-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="John">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Doe">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Email</label>
                                        <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="john@company.com">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Your Company">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Size</label>
                                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                            <option>1-10 employees</option>
                                            <option>11-50 employees</option>
                                            <option>51-200 employees</option>
                                            <option>201-500 employees</option>
                                            <option>500+ employees</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tell us about your project</label>
                                        <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Describe your business needs and goals..."></textarea>
                                    </div>

                                    <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-lg text-lg font-semibold hover:shadow-lg transition-all">
                                        Request Demo & Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl p-8 mb-8">
                                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Why Choose Us?</h2>
                                    <div class="space-y-6">
                                        <div class="flex items-start">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 mt-1">
                                                <span class="text-white text-sm">⚡</span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800 mb-1">Quick Implementation</h3>
                                                <p class="text-gray-600">Get up and running in days, not months</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 mt-1">
                                                <span class="text-white text-sm">🎯</span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800 mb-1">Tailored Solutions</h3>
                                                <p class="text-gray-600">Customized to fit your specific business needs</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 mt-1">
                                                <span class="text-white text-sm">👥</span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800 mb-1">Dedicated Support</h3>
                                                <p class="text-gray-600">Personal customer success manager</p>
                                            </div>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-4 mt-1">
                                                <span class="text-white text-sm">💰</span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800 mb-1">ROI Guarantee</h3>
                                                <p class="text-gray-600">See measurable results within 90 days</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Direct Contact -->
                                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                                    <h3 class="text-xl font-bold text-gray-800 mb-6">Prefer to Talk?</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-4">
                                                <span class="text-white">📞</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Sales Hotline</p>
                                                <p class="text-gray-600">+1 (800) 123-4567</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-4">
                                                <span class="text-white">✉️</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Sales Email</p>
                                                <p class="text-gray-600">sales@saasplatform.com</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-4">
                                                <span class="text-white">🕐</span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">Business Hours</p>
                                                <p class="text-gray-600">Mon-Fri: 9AM-6PM PST</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Contact Sales - Get Custom Quote & Demo',
            'Ready to transform your business? Contact our sales experts to find the perfect plan and get a personalized demo of our SaaS platform.'
        );
    }

    private function createPricingPage(): void
    {
        $announcement = Announcement::create([
            'title' => [
                'en' => 'Pricing Plans'
            ],
            'slug' => [
                'en' => 'pricing'
            ],
            'body' => [
                'en' => '<div class="container mx-auto px-4 py-16">
                    <div class="text-center mb-16">
                        <h1 class="text-5xl font-bold text-gray-800 mb-6">Simple, Transparent Pricing</h1>
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                            Choose the perfect plan for your business. All plans include core features with no hidden fees.
                        </p>
                    </div>

                    <!-- Pricing Cards -->
                    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto mb-16">
                        <!-- Starter Plan -->
                        <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-200 hover:shadow-xl transition-shadow">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4">Starter</h3>
                                <div class="mb-4">
                                    <span class="text-4xl font-bold text-gray-800">$29</span>
                                    <span class="text-gray-600">/month</span>
                                </div>
                                <p class="text-gray-600">Perfect for small teams getting started</p>
                            </div>

                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Up to 5 team members</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">10 projects</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Basic analytics</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Email support</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">50+ integrations</span>
                                </li>
                            </ul>

                            <button class="w-full bg-gradient-to-r from-gray-700 to-gray-800 text-white py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Start Free Trial
                            </button>
                        </div>

                        <!-- Professional Plan (Popular) -->
                        <div class="bg-white rounded-3xl shadow-xl p-8 border-2 border-indigo-500 hover:shadow-2xl transition-shadow relative">
                            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full text-sm font-semibold">
                                    Most Popular
                                </span>
                            </div>

                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4">Professional</h3>
                                <div class="mb-4">
                                    <span class="text-4xl font-bold text-gray-800">$79</span>
                                    <span class="text-gray-600">/month</span>
                                </div>
                                <p class="text-gray-600">Best for growing businesses</p>
                            </div>

                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Up to 25 team members</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Unlimited projects</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Advanced analytics</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Priority support</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">500+ integrations</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Custom workflows</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">API access</span>
                                </li>
                            </ul>

                            <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Start Free Trial
                            </button>
                        </div>

                        <!-- Enterprise Plan -->
                        <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-200 hover:shadow-xl transition-shadow">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4">Enterprise</h3>
                                <div class="mb-4">
                                    <span class="text-4xl font-bold text-gray-800">$199</span>
                                    <span class="text-gray-600">/month</span>
                                </div>
                                <p class="text-gray-600">For large organizations</p>
                            </div>

                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Unlimited team members</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Unlimited projects</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Enterprise analytics</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">24/7 phone support</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">All integrations</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Custom features</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">Dedicated account manager</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="text-green-500 mr-3">✓</span>
                                    <span class="text-gray-600">SLA guarantee</span>
                                </li>
                            </ul>

                            <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
                                Contact Sales
                            </button>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="max-w-4xl mx-auto">
                        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Frequently Asked Questions</h2>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-3">Can I change plans anytime?</h4>
                                <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-3">Is there a free trial?</h4>
                                <p class="text-gray-600">Yes, we offer a 14-day free trial for all plans. No credit card required.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-3">What payment methods do you accept?</h4>
                                <p class="text-gray-600">We accept all major credit cards, PayPal, and wire transfers for Enterprise plans.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-3">Do you offer discounts?</h4>
                                <p class="text-gray-600">Yes, we offer 20% off for annual payments and special discounts for non-profits.</p>
                            </div>
                        </div>
                    </div>
                </div>'
            ],
            'is_active' => true,
        ]);

        $this->createSeoSetting(
            $announcement,
            'Pricing Plans - Affordable SaaS Solutions',
            'Simple, transparent pricing for all business sizes. Choose from Starter ($29), Professional ($79), or Enterprise ($199) plans with 14-day free trial.'
        );
    }

    private function createSeoSetting($announcement, $title, $description): void
    {
        // Eğer bu sayfa için zaten SEO ayarı varsa oluşturma
        if ($announcement->seoSetting()->exists()) {
            return;
        }

        $announcement->seoSetting()->create([
            'titles' => [
                'en' => $title
            ],
            'descriptions' => [
                'en' => $description
            ],
            'robots_meta' => ['index' => true, 'follow' => true, 'archive' => true],
            'og_titles' => [
                'en' => $title
            ],
            'og_descriptions' => [
                'en' => $description
            ],
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'seo_score' => rand(80, 95),
        ]);
    }
}
