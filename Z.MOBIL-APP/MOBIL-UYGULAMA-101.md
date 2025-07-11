# 📱 Mobil Uygulama Geliştirme 101 - Sıfırdan Başlangıç

## 🎯 Bu Rehber Nedir?

Bu rehber, hiç mobil uygulama deneyimi olmayan kişiler için hazırlanmış, A'dan Z'ye mobil uygulama geliştirme rehberidir. Alfabeyi öğreten bir öğretmen gibi, her şeyi adım adım öğreteceğiz.

---

## 🤔 Mobil Uygulama Nedir?

**Basit Anlatım**: Telefonunuzda kullandığınız WhatsApp, Instagram, YouTube gibi programlar birer mobil uygulamadır.

**Teknik Anlatım**: Akıllı telefonlarda (iOS/Android) çalışan, kullanıcıların ihtiyaçlarını karşılayan yazılımlardır.

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

### 🚀 1. Flutter SDK Kurulumu

**Windows için:**
```bash
# 1. Flutter'ı indir
https://docs.flutter.dev/get-started/install/windows

# 2. C:\ dizinine çıkart
C:\flutter\

# 3. PATH'e ekle
C:\flutter\bin

# 4. Kontrol et
flutter doctor
```

**macOS için:**
```bash
# 1. Flutter'ı indir
https://docs.flutter.dev/get-started/install/macos

# 2. /Users/kullanici/flutter/ dizinine çıkart

# 3. Terminal'de PATH'e ekle
echo 'export PATH="$PATH:$HOME/flutter/bin"' >> ~/.zshrc
source ~/.zshrc

# 4. Kontrol et
flutter doctor
```

### 📱 2. Android Studio Kurulumu

1. **İndir**: https://developer.android.com/studio
2. **Kur**: Varsayılan ayarlarla
3. **SDK Manager'ı aç**
4. **Android SDK'ları indir**
5. **AVD (Emulator) oluştur**

### 💻 3. VS Code Kurulumu

1. **İndir**: https://code.visualstudio.com/
2. **Kur**: Varsayılan ayarlarla
3. **Flutter Extension'ı yükle**
4. **Dart Extension'ı yükle**

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

## 🗂️ 10. ADIM: Proje Planlama

### 📋 Bizim Projemiz İçin Plan

**1. Hafta: Temel Kurulum**
- Flutter kurulumu
- İlk uygulama oluşturma
- Hello World çalıştırma

**2. Hafta: UI Temelleri**
- Widget'ları öğrenme
- Basit ekranlar tasarlama
- Navigation (sayfa geçişleri)

**3. Hafta: API Entegrasyonu**
- HTTP istekleri
- Login ekranı
- Token yönetimi

**4. Hafta: Özel Özellikler**
- Profil sayfası
- Ayarlar
- Offline destek

### 🎯 Hedefler

**Kısa Vadeli (1 ay):**
- Flutter temelleri
- Basit uygulama yapabilme
- API bağlantısı kurabilme

**Orta Vadeli (3 ay):**
- Kompleks UI tasarlayabilme
- State management
- Veritabanı entegrasyonu

**Uzun Vadeli (6 ay):**
- Play Store'da yayınlama
- iOS App Store'da yayınlama
- Kullanıcı geri bildirimlerini değerlendirme

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

Bu rehber size mobil uygulama geliştirme yolculuğunuzda rehberlik edecek. Her adımı dikkatli bir şekilde takip edin ve bol bol pratik yapın.

**Unutmayın:**
- Sabırlı olun - öğrenme süreci zaman alır
- Bol pratik yapın - teori tek başına yeterli değil
- Topluluktan yardım alın - yalnız değilsiniz
- Küçük projelerle başlayın - büyük hayaller kurmayın

**İyi şanslar ve keyifli kodlamalar! 🚀**

---

*Bu rehber sürekli güncellenecek ve deneyimlerinize göre genişletilecektir.*
*Son güncelleme: 11.07.2025*