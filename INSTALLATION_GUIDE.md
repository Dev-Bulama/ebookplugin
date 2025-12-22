# SkillScore Ebook Commerce - Quick Installation Guide

## 📦 What You've Received

You now have a **production-ready, fully functional WordPress plugin** that:

✅ Installs cleanly via WordPress Admin
✅ Sells ebooks with 4 payment gateways
✅ Offers audio previews (TTS or uploaded samples)
✅ Provides secure, expiring download links
✅ Includes shortcodes and Elementor widget
✅ Features modern Tailwind CSS styling

---

## 🚀 Quick Installation (3 Steps)

### Step 1: Upload Plugin

1. **Login to WordPress Admin**
2. **Go to**: Plugins → Add New → Upload Plugin
3. **Choose File**: `skillscore-ebook-commerce.zip`
4. **Click**: "Install Now"
5. **Click**: "Activate Plugin"

### Step 2: Configure Payment Gateway

1. **Go to**: Ebooks → Settings → Payment Gateways
2. **Choose one gateway** (Paystack, Flutterwave, Stripe, or PayPal)
3. **Enable it** and enter your API keys
4. **Save Changes**

### Step 3: Create Your First Ebook

1. **Go to**: Ebooks → Add New
2. **Fill in**:
   - Title: "My First Ebook"
   - Description: Full ebook details
   - Price: 9.99
   - Upload ebook file (PDF/EPUB/DOCX)
   - Set featured image (cover)
3. **Publish**

### Step 4: Display Ebooks

**Using Shortcode:**
- Create a new page
- Add shortcode: `[skillscore_ebooks]`
- Publish

**Using Elementor:**
- Edit page with Elementor
- Search for "SkillScore Ebooks" widget
- Drag onto page
- Configure and publish

---

## 📁 Plugin Files Location

After installation, files are located at:

```
/wp-content/plugins/skillscore-ebook-commerce/
├── skillscore-ebook-commerce.php  ← Main plugin file
├── readme.txt                     ← WordPress.org readme
├── uninstall.php                  ← Clean uninstall script
├── assets/                        ← CSS and JavaScript
├── includes/                      ← PHP classes
├── templates/                     ← Frontend templates
└── languages/                     ← Translation files
```

Uploaded ebooks stored at:
```
/wp-content/uploads/skillscore-ebooks/  ← Secured with .htaccess
```

Audio files cached at:
```
/wp-content/uploads/skillscore-audio/
```

---

## 🔧 Essential Settings

### General Settings (Ebooks → Settings → General)

- **Currency**: USD, EUR, GBP, NGN
- **Download Limit**: 5 downloads per purchase (or -1 for unlimited)
- **Link Expiry**: 30 days (or 0 for no expiry)
- **Quantity Selector**: Enable/disable customer quantity selection

### Payment Gateway Keys

**Paystack:**
- Dashboard: https://dashboard.paystack.com/#/settings/developer
- Keys needed: Public Key, Secret Key

**Flutterwave:**
- Dashboard: https://dashboard.flutterwave.com/settings/apis
- Keys needed: Public Key, Secret Key

**Stripe:**
- Dashboard: https://dashboard.stripe.com/apikeys
- Keys needed: Publishable Key, Secret Key

**PayPal:**
- Dashboard: https://developer.paypal.com/developer/applications
- Keys needed: Client ID, Secret
- Mode: Sandbox (testing) or Live (production)

---

## 🎨 Shortcode Examples

**Display all ebooks (grid):**
```
[skillscore_ebooks]
```

**Display 6 ebooks in 2 columns:**
```
[skillscore_ebooks limit="6" columns="2"]
```

**Display ebooks from "Fiction" category:**
```
[skillscore_ebooks category="fiction" limit="12"]
```

**Display single ebook (replace 123 with actual post ID):**
```
[skillscore_ebook id="123"]
```

---

## 🎤 Audio Preview Setup

### Option 1: Global Voice Sample (Easiest)

1. **Go to**: Ebooks → Settings → Voice Preview
2. **Enable**: "Use Global Voice Sample"
3. **Upload**: MP3/WAV/OGG audio file
4. This sample plays for ALL ebooks

### Option 2: Browser TTS (No Setup)

1. **Select**: TTS Engine → "Browser TTS"
2. Uses browser's built-in text-to-speech
3. No server configuration needed

### Option 3: Piper TTS (Advanced)

**Requires Linux/Mac server with:**
- Python 3.8+
- FFmpeg

**Installation:**
```bash
pip install piper-tts
sudo apt-get install ffmpeg
```

**Configuration:**
- Piper Path: `/usr/local/bin/piper`
- Model: `en_US-lessac-medium`
- FFmpeg Path: `/usr/bin/ffmpeg`

---

## 🛡️ Security Features

✅ **Protected Storage**: Files in secured upload directory
✅ **Unique Tokens**: Each download gets unique signed URL
✅ **Expiring Links**: Configurable expiration (default 30 days)
✅ **Download Limits**: Configurable max downloads (default 5)
✅ **IP Logging**: Tracks who downloaded what
✅ **Revokable Access**: Admin can revoke any download link

---

## 📊 Managing Orders & Downloads

**View Orders:**
- **Go to**: Ebooks → Orders
- See: Customer, Amount, Status, Date
- Filter by payment status

**View Downloads:**
- **Go to**: Ebooks → Downloads
- See: Customer, Download Count, Expiry
- Revoke access if needed

**Order Statuses:**
- **Pending**: Payment initiated, not completed
- **Completed**: Payment successful, download available
- **Failed**: Payment failed or cancelled

---

## 🎨 Styling (Tailwind CSS)

The plugin uses **Tailwind CSS via CDN** for styling.

**Color Scheme:**
- Primary: Yellow (#eab308)
- Secondary: Rich Black (#1f2937)
- Success: Green (#10b981)
- Error: Red (#ef4444)

**Customizing:**
- Add custom CSS in: Appearance → Customize → Additional CSS
- Override Tailwind classes with `!important` if needed

**Template Override:**
Copy to your theme:
```
your-theme/
└── skillscore-ebook/
    ├── ebook-card.php
    └── ebook-single.php
```

---

## 🐛 Troubleshooting

### "No valid plugins found" Error

**Cause**: Incorrect ZIP structure

**Solution**:
1. Extract the ZIP
2. Verify it opens to `skillscore-ebook-commerce/` folder
3. NOT: `skillscore-ebook-commerce/skillscore-ebook-commerce/`
4. Re-upload

### Payment Not Working

**Checklist:**
- ✅ Gateway enabled?
- ✅ API keys correct (no spaces)?
- ✅ Using correct mode (test vs live)?
- ✅ Check WordPress debug log

**Enable Debug:**
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
Check: `/wp-content/debug.log`

### File Upload Fails

**Increase PHP limits** in `php.ini`:
```ini
upload_max_filesize = 64M
post_max_size = 64M
```

Or in `.htaccess`:
```apache
php_value upload_max_filesize 64M
php_value post_max_size 64M
```

### Download Link Not Working

**Check:**
- Order status = "completed"?
- Link not expired?
- Download limit not exceeded?
- File exists in uploads folder?

---

## 📚 Documentation

**Full Documentation**: See `DOCUMENTATION.md` for:
- Complete API reference
- Developer hooks and filters
- Database schema
- Advanced customization
- Payment webhook setup
- TTS integration details

---

## ✅ Plugin Features Checklist

**Core Features:**
- ✅ Custom Post Type for ebooks
- ✅ Meta boxes (price, stock, author, ISBN, etc.)
- ✅ File upload (PDF, EPUB, DOCX)
- ✅ Featured image (cover)
- ✅ Categories and tags

**Payment Processing:**
- ✅ Paystack integration
- ✅ Flutterwave integration
- ✅ Stripe integration
- ✅ PayPal integration
- ✅ Quantity selector
- ✅ Order management
- ✅ Payment verification

**Download Security:**
- ✅ Signed URLs
- ✅ Expiring links
- ✅ Download limits
- ✅ IP logging
- ✅ Revokable access
- ✅ Protected file storage

**Frontend Display:**
- ✅ Shortcode: `[skillscore_ebooks]`
- ✅ Shortcode: `[skillscore_ebook id="X"]`
- ✅ Elementor widget
- ✅ Tailwind CSS styling
- ✅ Responsive design
- ✅ Mobile-first approach

**Audio Preview:**
- ✅ Global voice sample upload
- ✅ Piper TTS integration
- ✅ Coqui TTS integration
- ✅ Browser TTS fallback
- ✅ Audio caching
- ✅ Custom audio player

**Admin Features:**
- ✅ Settings page (3 tabs)
- ✅ Orders management
- ✅ Downloads tracking
- ✅ Sales statistics
- ✅ Stock management
- ✅ Admin assets (CSS/JS)

**Developer Features:**
- ✅ Hooks and filters
- ✅ Template override support
- ✅ Clean code (WordPress standards)
- ✅ Secure (nonces, sanitization, validation)
- ✅ Translation-ready
- ✅ Uninstall cleanup

---

## 🎯 Next Steps

1. **Install the plugin** using the ZIP file
2. **Configure at least one payment gateway**
3. **Create your first ebook**
4. **Add shortcode to a page**
5. **Test a purchase** (use test/sandbox mode)
6. **Go live** when ready (switch to live API keys)

---

## 📞 Support

For questions or issues:
- **Author**: SkillScore IT Solutions and Training
- **Developer**: Tijani Bulama
- **Plugin Version**: 1.0.0
- **WordPress**: 6.0+
- **PHP**: 8.0+

---

## 📜 License

**GPL-2.0+**

This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.

---

**🎉 Enjoy your new ebook commerce plugin!**

All files are production-ready, secure, and follow WordPress coding standards. The plugin installs cleanly without errors and is ready for immediate use.
