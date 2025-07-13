# 📱 Mobil Uygulama Geliştirme 101 - Sıfırdan Başlangıç

## 🎯 Bu Rehber Nedir?

Bu rehber, hiç mobil uygulama deneyimi olmayan kişiler için hazırlanmış, A'dan Z'ye mobil uygulama geliştirme rehberidir. Alfabeyi öğreten bir öğretmen gibi, her şeyi adım adım öğreteceğiz.

---

## 🤔 Mobil Uygulama Nedir?

**Basit Anlatım**: Telefonunuzda kullandığınız WhatsApp, Instagram, YouTube gibi programlar birer mobil uygulamadır.

**Teknik Anlatım**: Akıllı telefonlarda (iOS/Android) çalışan, kullanıcıların ihtiyaçlarını karşılayan yazılımlardır.

## ⚠️ ÖNEMLİ NOT - İŞ BÖLÜMÜ

**🤖 BENİM GÖREVLERIM (Claude):**
- Tüm kodları yazacağım
- Uygulamaları geliştirecğim  
- Hataları çözeceğim
- Özellik ekleyeceğim

**👨‍💻 SENİN GÖREVİN (Nurullah):**
- Sadece kurulum yapacaksın
- Programları bilgisayarına yükleyeceksin
- Emülatörü çalıştıracaksın
- Uygulamayı test edeceksin

**📝 YAPACAĞIN TEK ŞEY:**
1. Flutter'ı kur
2. Android Studio'yu kur  
3. VS Code'u kur
4. Emülatörü çalıştır
5. Bana "hazır" de, gerisini ben hallederim!

**🚫 YAPMAYACAGIN ŞEYLER:**
- Kod yazmak
- Widget öğrenmek
- API entegrasyonu
- Debugging
- Problem çözme

**🎯 HEDEF:**
Sen sadece "kurulum teknisyeni" olacaksın, ben "yazılım geliştirici" olacağım!

---

## 📚 1. ADIM: Temel Kavramlar

### 📱 Platform Türleri

**Android (Google)**
- Dünyada %70 kullanım oranı
- Google Play Store'da yayınlanır
- Java, Kotlin dilleriyle yazılır

**iOS (Apple)**
- Dünyada %30 kullanım oranı
- App Store'da yayınlanır
- Swift, Objective-C dilleriyle yazılır

**Cross-Platform (İki Platform Birden)**
- Tek kodla hem Android hem iOS
- Flutter, React Native gibi teknolojiler
- Daha ekonomik ve hızlı

---

## 🛠️ 2. ADIM: Hangi Teknolojiyi Seçmeli?

### 🎯 Bizim Durumumuz İçin Tavsiye: **Flutter**

**Neden Flutter?**
- ✅ Tek kod, iki platform (Android + iOS)
- ✅ Google tarafından destekleniyor
- ✅ Öğrenmesi kolay
- ✅ Hızlı geliştirme
- ✅ Laravel API ile kolay entegrasyon

**Diğer Seçenekler:**
- **React Native** (Facebook'un teknolojisi)
- **Native** (Ayrı ayrı Android ve iOS kodu)
- **Ionic** (Web teknolojileri ile)

---

## 💻 3. ADIM: Bilgisayar Hazırlığı

### 🖥️ Minimum Sistem Gereksinimleri

**Windows:**
- Windows 10 (64-bit)
- 8 GB RAM (16 GB önerilir)
- 30 GB boş disk alanı
- İnternet bağlantısı

**macOS:**
- macOS 10.14 veya üzeri
- 8 GB RAM (16 GB önerilir)
- 30 GB boş disk alanı
- Xcode (iOS için gerekli)

---

## 🔧 4. ADIM: Kurulum - Adım Adım

### 📋 Kurulum Sırası

1. **Flutter SDK**
2. **Android Studio**
3. **VS Code** (kod editörü)
4. **Git** (versiyon kontrolü)

## 🔗 DOWNLOAD LİNKLERİ - HEMEN İNDİR

### 🚀 1. Flutter SDK - TEK TIKLA İNDİR

**💻 Windows (64-bit):**
- **Direkt İndirme:** https://storage.googleapis.com/flutter_infra_release/releases/stable/windows/flutter_windows_3.16.9-stable.zip
- **Alternatif:** https://docs.flutter.dev/get-started/install/windows

**🍎 macOS (Intel):**
- **Direkt İndirme:** https://storage.googleapis.com/flutter_infra_release/releases/stable/macos/flutter_macos_3.16.9-stable.zip
- **Alternatif:** https://docs.flutter.dev/get-started/install/macos

**🍎 macOS (Apple Silicon/M1/M2):**
- **Direkt İndirme:** https://storage.googleapis.com/flutter_infra_release/releases/stable/macos/flutter_macos_arm64_3.16.9-stable.zip
- **Alternatif:** https://docs.flutter.dev/get-started/install/macos

### 📱 2. Android Studio - TEK TIKLA İNDİR

**💻 Windows:**
- **Direkt İndirme:** https://redirector.gvt1.com/edgedl/android/studio/install/2023.1.1.28/android-studio-2023.1.1.28-windows.exe
- **Ana Sayfa:** https://developer.android.com/studio

**🍎 macOS (Intel):**
- **Direkt İndirme:** https://redirector.gvt1.com/edgedl/android/studio/install/2023.1.1.28/android-studio-2023.1.1.28-mac.dmg
- **Ana Sayfa:** https://developer.android.com/studio

**🍎 macOS (Apple Silicon):**
- **Direkt İndirme:** https://redirector.gvt1.com/edgedl/android/studio/install/2023.1.1.28/android-studio-2023.1.1.28-mac_arm.dmg
- **Ana Sayfa:** https://developer.android.com/studio

### 💻 3. VS Code - TEK TIKLA İNDİR

**💻 Windows:**
- **Direkt İndirme:** https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-user
- **Ana Sayfa:** https://code.visualstudio.com/

**🍎 macOS (Intel):**
- **Direkt İndirme:** https://code.visualstudio.com/sha/download?build=stable&os=darwin
- **Ana Sayfa:** https://code.visualstudio.com/

**🍎 macOS (Apple Silicon):**
- **Direkt İndirme:** https://code.visualstudio.com/sha/download?build=stable&os=darwin-arm64
- **Ana Sayfa:** https://code.visualstudio.com/

### 🔄 4. Git - TEK TIKLA İNDİR

**💻 Windows:**
- **Direkt İndirme:** https://github.com/git-for-windows/git/releases/download/v2.43.0.windows.1/Git-2.43.0-64-bit.exe
- **Ana Sayfa:** https://git-scm.com/download/win

**🍎 macOS:**
- **Homebrew ile:** `brew install git`
- **Direkt İndirme:** https://git-scm.com/download/mac
- **Ana Sayfa:** https://git-scm.com/download/mac

### 🎯 VS CODE EXTENSİONLARI - ZORUNLU

**Flutter Extension:**
- **Direkt Link:** https://marketplace.visualstudio.com/items?itemName=Dart-Code.flutter
- **VS Code içinde:** `Ctrl+Shift+X` → "Flutter" ara → kur

**Dart Extension:**
- **Direkt Link:** https://marketplace.visualstudio.com/items?itemName=Dart-Code.dart-code
- **VS Code içinde:** `Ctrl+Shift+X` → "Dart" ara → kur

## ⚡ HIZLI KURULUM REHBERİ

### 🚀 1. Flutter SDK Kurulumu

**Windows için:**
```bash
# 1. Yukarıdaki linkten Flutter'ı indir
# 2. C:\ dizinine çıkart
C:\flutter\

# 3. PATH'e ekle (Sistem Özellikleri → Gelişmiş → Ortam Değişkenleri)
C:\flutter\bin

# 4. Komut istemini aç ve kontrol et
flutter doctor
```

**macOS için:**
```bash
# 1. Yukarıdaki linkten Flutter'ı indir
# 2. /Users/kullanici/flutter/ dizinine çıkart

# 3. Terminal'de PATH'e ekle
echo 'export PATH="$PATH:$HOME/flutter/bin"' >> ~/.zshrc
source ~/.zshrc

# 4. Kontrol et
flutter doctor
```

### 📱 2. Android Studio Kurulumu

1. **İndir**: Yukarıdaki direkt linkten
2. **Kur**: Varsayılan ayarlarla
3. **SDK Manager'ı aç** (Tools → SDK Manager)
4. **Android SDK'ları indir** (API 30, 31, 32, 33)
5. **AVD (Emulator) oluştur** (Tools → AVD Manager)

### 💻 3. VS Code Kurulumu

1. **İndir**: Yukarıdaki direkt linkten
2. **Kur**: Varsayılan ayarlarla
3. **Flutter Extension'ı yükle** (yukarıdaki linkten)
4. **Dart Extension'ı yükle** (yukarıdaki linkten)

---

## 📱 5. ADIM: İlk Uygulama Oluşturma

### 🎉 Hello World Uygulaması

```bash
# 1. Yeni proje oluştur
flutter create my_first_app

# 2. Proje dizinine gir
cd my_first_app

# 3. Emulator'u başlat
flutter emulators --launch <emulator_id>

# 4. Uygulamayı çalıştır
flutter run
```

### 🔍 Proje Yapısı

```
my_first_app/
├── android/          # Android özel dosyalar
├── ios/             # iOS özel dosyalar
├── lib/             # Ana kod dosyaları
│   └── main.dart    # Ana dosya
├── pubspec.yaml     # Paket yönetimi
└── test/            # Test dosyaları
```

---

## 🎨 6. ADIM: Flutter Temelleri

### 🧩 Widget Nedir?

Flutter'da **her şey widget'tır**. Buton, metin, resim, layout... hepsi widget.

```dart
// Basit bir metin widget'ı
Text('Merhaba Dünya!')

// Basit bir buton widget'ı
ElevatedButton(
  onPressed: () {
    print('Butona tıklandı!');
  },
  child: Text('Tıkla'),
)
```

### 🎯 Temel Widget'lar

**Metin Widget'ları:**
- `Text()` - Metin gösterme
- `RichText()` - Zengin metin

**Buton Widget'ları:**
- `ElevatedButton()` - Yükseltilmiş buton
- `TextButton()` - Metin buton
- `IconButton()` - İkon buton

**Layout Widget'ları:**
- `Column()` - Dikey sıralama
- `Row()` - Yatay sıralama
- `Container()` - Kutu/kapsayıcı

**Input Widget'ları:**
- `TextField()` - Metin girişi
- `Checkbox()` - Onay kutusu
- `Switch()` - Açma/kapama

---

## 🌐 7. ADIM: API Entegrasyonu

### 🔗 Laravel API ile Bağlantı

**1. HTTP Paketini Ekle**

`pubspec.yaml` dosyasına:
```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0  # API istekleri için
```

**2. API Çağrısı Yapma**

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

// Login fonksiyonu
Future<Map<String, dynamic>> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('https://laravel.test/api/v1/auth/login'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'email': email,
      'password': password,
    }),
  );
  
  if (response.statusCode == 200) {
    return jsonDecode(response.body);
  } else {
    throw Exception('Login başarısız');
  }
}
```

**3. Token Saklama**

```dart
import 'package:shared_preferences/shared_preferences.dart';

// Token'ı sakla
Future<void> saveToken(String token) async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  await prefs.setString('token', token);
}

// Token'ı al
Future<String?> getToken() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  return prefs.getString('token');
}
```

---

## 🎨 8. ADIM: UI/UX Tasarımı

### 🎭 Material Design vs Cupertino

**Material Design (Android tarzı):**
```dart
MaterialApp(
  home: Scaffold(
    appBar: AppBar(title: Text('Uygulama')),
    body: Center(child: Text('Merhaba')),
  ),
)
```

**Cupertino (iOS tarzı):**
```dart
CupertinoApp(
  home: CupertinoPageScaffold(
    navigationBar: CupertinoNavigationBar(
      middle: Text('Uygulama'),
    ),
    child: Center(child: Text('Merhaba')),
  ),
)
```

### 🌈 Renkler ve Temalar

```dart
MaterialApp(
  theme: ThemeData(
    primarySwatch: Colors.blue,
    accentColor: Colors.orange,
    fontFamily: 'Roboto',
  ),
  home: MyApp(),
)
```

---

## 📚 9. ADIM: Öğrenme Kaynakları

### 📖 Ücretsiz Kaynaklar

**Resmi Dokümantasyon:**
- https://flutter.dev/docs
- https://dart.dev/guides

**YouTube Kanalları:**
- Flutter Official Channel
- The Net Ninja - Flutter Tutorial
- Flutter Mapp

**Türkçe Kaynaklar:**
- FlutterTR YouTube kanalı
- Flutter Türkiye Facebook grubu
- Medium'da Flutter Türkiye yayınları

### 💰 Ücretli Kurslar

- Udemy Flutter kursları
- Pluralsight Flutter path
- LinkedIn Learning Flutter

---

## 🗂️ 10. ADIM: Bizim Proje Planımız

### 📋 SADECE KURULUM YAPACAKSIN - İŞ PLANI

**🗓️ 1. GÜN (Sadece Kurulum):**
- Flutter SDK indir ve kur
- Android Studio indir ve kur
- VS Code indir ve kur
- Git indir ve kur

**🗓️ 2. GÜN (Test ve Hazırlık):**
- Emülatör oluştur
- `flutter doctor` çalıştır
- Bana "kurulum tamam" de

**🗓️ 3. GÜN ve SONRASI (Ben Çalışacağım):**
- Ben kodları yazacağım
- Sen sadece test edeceksin
- "Çalışıyor mu?" sorusuna cevap vereceksin

### 🎯 Gerçekçi Hedefler

**🚀 HEMEN (2 Gün):**
- Kurulum tamam
- Emülatör çalışıyor
- Bana "hazırım" diyebilirsin

**📱 1 HAFTA:**
- İlk uygulama çalışacak (ben yapacağım)
- Sen sadece test edeceksin

**🏢 2 HAFTA:**
- Laravel'e bağlı mobil admin paneli (ben yapacağım)
- Sen sadece kullanacaksın

**🎉 1 AY:**
- Tam çalışır uygulama
- Play Store'a yükleme (ben yapacağım)

---

## 🚀 11. ADIM: İlk Proje - Login Uygulaması

### 🎯 Proje Hedefi

Laravel API'mize bağlanan, kullanıcı girişi yapabilen basit bir uygulama.

### 📋 Özellikler

1. **Splash Screen** - Açılış ekranı
2. **Login Screen** - Giriş ekranı
3. **Home Screen** - Ana ekran
4. **Profile Screen** - Profil ekranı

### 🔧 Gerekli Paketler

```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0              # API istekleri
  shared_preferences: ^2.2.2 # Veri saklama
  provider: ^6.0.5          # State management
```

### 📱 Ekran Tasarımı

**Login Ekranı:**
```dart
class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Giriş Yap')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                labelText: 'Email',
                border: OutlineInputBorder(),
              ),
            ),
            SizedBox(height: 16),
            TextField(
              controller: _passwordController,
              decoration: InputDecoration(
                labelText: 'Şifre',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            SizedBox(height: 24),
            ElevatedButton(
              onPressed: () {
                // Login işlemi
                _login();
              },
              child: Text('Giriş Yap'),
            ),
          ],
        ),
      ),
    );
  }
  
  void _login() async {
    // API çağrısı burada yapılacak
    print('Login: ${_emailController.text}');
  }
}
```

---

## 📤 12. ADIM: Uygulama Yayınlama

### 🏪 Google Play Store

**Gereksinimler:**
- Google Play Console hesabı ($25 tek seferlik)
- APK veya AAB dosyası
- Uygulama açıklaması
- Ekran görüntüleri
- Gizlilik politikası

**Adımlar:**
1. APK/AAB oluştur: `flutter build apk`
2. Play Console'da yeni uygulama oluştur
3. Dosyaları yükle
4. Açıklama ve görselleri ekle
5. Yayın için gönder

### 🍎 Apple App Store

**Gereksinimler:**
- Apple Developer hesabı ($99/yıl)
- macOS bilgisayar
- Xcode
- iOS cihaz veya simulator

**Adımlar:**
1. iOS build: `flutter build ios`
2. Xcode ile açp
3. App Store Connect'te uygulama oluştur
4. Archive ve upload
5. Review için gönder

---

## ⚡ 13. ADIM: Performans Optimizasyonu

### 🚀 Hız Artırma

**1. Widget Optimizasyonu:**
```dart
// Yanlış kullanım
Container(
  child: Container(
    child: Text('Merhaba'),
  ),
)

// Doğru kullanım
Text('Merhaba')
```

**2. State Management:**
```dart
// Provider kullanımı
ChangeNotifierProvider(
  create: (context) => UserProvider(),
  child: MyApp(),
)
```

**3. Resim Optimizasyonu:**
```dart
// Resim cache'leme
CachedNetworkImage(
  imageUrl: "https://example.com/image.jpg",
  placeholder: (context, url) => CircularProgressIndicator(),
  errorWidget: (context, url, error) => Icon(Icons.error),
)
```

### 📊 Performans Ölçümü

```bash
# Performans profili
flutter run --profile

# Build size analizi
flutter build apk --analyze-size
```

---

## 🐛 14. ADIM: Hata Ayıklama

### 🔍 Debug Teknikleri

**1. Print Statement:**
```dart
print('Değişken değeri: $variable');
```

**2. Debugger:**
```dart
import 'dart:developer';

void myFunction() {
  debugger(); // Breakpoint
  print('Bu satır çalışacak');
}
```

**3. Flutter Inspector:**
- VS Code'da Flutter Inspector
- Widget tree görüntüleme
- Layout problemlerini bulma

### 🚨 Yaygın Hatalar

**1. Null Safety:**
```dart
// Hata
String name = null;

// Doğru
String? name = null;
```

**2. Async/Await:**
```dart
// Hata
void getData() {
  http.get(url);
}

// Doğru
Future<void> getData() async {
  await http.get(url);
}
```

---

## 🎯 15. ADIM: Sonraki Adımlar

### 📈 İleri Seviye Konular

**1. State Management:**
- Provider
- Riverpod
- Bloc
- GetX

**2. Veritabanı:**
- SQLite
- Hive
- Firebase

**3. Push Notification:**
- Firebase Cloud Messaging
- Local Notifications

**4. Testing:**
- Unit Testing
- Widget Testing
- Integration Testing

### 🌟 Proje Fikirleri

**Başlangıç Seviyesi:**
- Hesap Makinesi
- Todo List
- Hava Durumu
- QR Code Reader

**Orta Seviye:**
- E-ticaret uygulaması
- Sosyal medya uygulaması
- Chat uygulaması
- Finans uygulaması

**İleri Seviye:**
- Oyun uygulaması
- AR/VR uygulaması
- IoT uygulaması
- AI entegrasyonu

---

## 📚 16. ADIM: Faydalı Kaynaklar

### 🔗 Linkler

**Resmi Siteler:**
- https://flutter.dev/
- https://dart.dev/
- https://pub.dev/ (paket deposu)

**Topluluk:**
- https://reddit.com/r/FlutterDev
- https://discord.gg/flutter
- https://stackoverflow.com/questions/tagged/flutter

**Tasarım:**
- https://material.io/design
- https://developer.apple.com/design/human-interface-guidelines/

### 📱 Örnek Uygulamalar

**GitHub Repositories:**
- https://github.com/flutter/samples
- https://github.com/iampawan/FlutterExampleApps
- https://github.com/FilledStacks/flutter-tutorials

---

## 🎓 Sonuç

**📱 SENİN İÇİN ÖZET:**

**✅ YAPACAKLARIN:**
1. Yukarıdaki download linklerini kullan
2. Flutter SDK'yı kur
3. Android Studio'yu kur
4. VS Code'u kur
5. Emülatör oluştur
6. Bana "kurulum tamam" de

**❌ YAPMANAYACAKLARIN:**
- Kod öğrenmeye çalışma
- Widget'ları anlamaya çalışma  
- Tutorial'ları izleme
- Karmaşık şeyleri öğrenme

**🤖 BENİM YAPACAKLARIM:**
- Tüm kodları yazacağım
- Uygulamaları geliştireceğim
- Problemleri çözeceğim
- Laravel'e entegre edeceğim

**🎯 HEDEF:**
Sen sadece kurulum teknisyeni olacaksın. Gerisini ben halledeceğim!

**💡 İyi şanslar ve rahat kurulumlar! 🚀**

**📞 Kurulum bittikten sonra bana "hazırım" de, işe başlayalım!**

---

*Bu rehber sürekli güncellenecek ve deneyimlerinize göre genişletilecektir.*
*Son güncelleme: 11.07.2025*