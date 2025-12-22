# SkillScore Ebook Commerce - Complete Documentation

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [ZIP Structure](#zip-structure)
4. [Configuration](#configuration)
5. [Creating Ebooks](#creating-ebooks)
6. [Shortcodes](#shortcodes)
7. [Elementor Integration](#elementor-integration)
8. [Payment Gateways](#payment-gateways)
9. [Audio Preview System](#audio-preview-system)
10. [Download Security](#download-security)
11. [Troubleshooting](#troubleshooting)
12. [Developer Guide](#developer-guide)
13. [FAQ](#faq)

---

## Overview

**SkillScore Ebook Commerce** is a production-ready WordPress plugin that enables selling ebooks with:

- ✅ **4 Payment Gateways**: Paystack, Flutterwave, Stripe, PayPal
- ✅ **Audio Previews**: TTS integration (Piper/Coqui) or global voice samples
- ✅ **Secure Downloads**: Signed URLs, expiring links, download limits
- ✅ **Shortcodes**: Display ebooks anywhere
- ✅ **Elementor Widget**: Drag-and-drop integration
- ✅ **Tailwind CSS**: Modern, responsive UI via CDN
- ✅ **Stock Management**: Quantity tracking or unlimited digital copies
- ✅ **Sales Reports**: Orders and download tracking

**Plugin Metadata:**
- **Plugin Name**: SkillScore Ebook Commerce
- **Author**: SkillScore IT Solutions and Training
- **Developer**: Tijani Bulama
- **Version**: 1.0.0
- **License**: GPL-2.0+
- **PHP**: 8.0+
- **WordPress**: 6.0+

---

## Installation

### Method 1: WordPress Admin Upload (Recommended)

1. **Create ZIP file** (see ZIP Structure below)
2. **Login to WordPress Admin**
3. **Navigate to**: Plugins → Add New → Upload Plugin
4. **Choose file**: `skillscore-ebook-commerce.zip`
5. **Click**: "Install Now"
6. **Activate** the plugin

### Method 2: FTP/SFTP Upload

1. **Extract ZIP** to get the `skillscore-ebook-commerce` folder
2. **Upload** via FTP to: `/wp-content/plugins/`
3. **Navigate to**: Plugins in WordPress admin
4. **Activate**: SkillScore Ebook Commerce

### Method 3: WP-CLI

```bash
wp plugin install skillscore-ebook-commerce.zip --activate
```

---

## ZIP Structure

⚠️ **CRITICAL**: The ZIP must open directly into the plugin folder.

### Correct Structure

```
skillscore-ebook-commerce.zip
└── skillscore-ebook-commerce/
    ├── skillscore-ebook-commerce.php  ← REQUIRED ROOT FILE
    ├── readme.txt
    ├── uninstall.php
    ├── assets/
    │   ├── css/
    │   │   ├── public.css
    │   │   └── admin.css
    │   └── js/
    │       ├── public.js
    │       └── admin.js
    ├── includes/
    │   ├── class-activator.php
    │   ├── class-deactivator.php
    │   ├── class-ebook-core.php
    │   ├── class-ebook-cpt.php
    │   ├── class-shortcodes.php
    │   ├── class-elementor-widget.php
    │   ├── class-payment-handler.php
    │   ├── class-download-handler.php
    │   ├── class-voice-preview.php
    │   └── class-admin-settings.php
    ├── templates/
    │   ├── ebook-card.php
    │   └── ebook-single.php
    └── languages/
```

### Creating the ZIP

#### From Command Line:
```bash
cd /path/to/plugin-parent-directory
zip -r skillscore-ebook-commerce.zip skillscore-ebook-commerce/
```

#### From File Manager:
1. Right-click the `skillscore-ebook-commerce` folder
2. Choose "Compress" or "Create Archive"
3. Name it: `skillscore-ebook-commerce.zip`

⚠️ **Common Mistake**: Creating a ZIP with double nesting:
```
❌ WRONG:
skillscore-ebook-commerce.zip
└── skillscore-ebook-commerce/
    └── skillscore-ebook-commerce/  ← Double nesting causes "No valid plugins found"
```

---

## Configuration

### Step 1: Initial Setup

After activation:

1. **Navigate to**: Ebooks → Settings
2. **Configure General Settings**:
   - Currency: USD, EUR, GBP, NGN
   - Currency Symbol: $, €, £, ₦
   - Enable Quantity Selector: ✓
   - Download Limit: 5 (or -1 for unlimited)
   - Download Expiry: 30 days (or 0 for no expiry)

### Step 2: Payment Gateway Setup

Choose and configure at least one payment gateway.

#### Paystack (Recommended for Nigeria/Africa)

1. **Go to**: [Paystack Dashboard](https://dashboard.paystack.com/#/settings/developer)
2. **Copy**: Public Key and Secret Key
3. **In WordPress**: Ebooks → Settings → Payment Gateways
4. **Enable Paystack**: ✓
5. **Paste** API keys
6. **Save Changes**

#### Flutterwave (Pan-African)

1. **Go to**: [Flutterwave Dashboard](https://dashboard.flutterwave.com/settings/apis)
2. **Copy**: Public Key and Secret Key
3. **Enable Flutterwave**: ✓
4. **Paste** API keys
5. **Save Changes**

#### Stripe (Global)

1. **Go to**: [Stripe Dashboard](https://dashboard.stripe.com/apikeys)
2. **Copy**: Publishable Key and Secret Key
3. **Enable Stripe**: ✓
4. **Paste** API keys
5. **Save Changes**

#### PayPal (Worldwide)

1. **Go to**: [PayPal Developer](https://developer.paypal.com/developer/applications)
2. **Create App** → Copy Client ID and Secret
3. **Choose Mode**: Sandbox (testing) or Live (production)
4. **Enable PayPal**: ✓
5. **Paste** credentials
6. **Save Changes**

---

## Creating Ebooks

### Adding a New Ebook

1. **Navigate to**: Ebooks → Add New
2. **Enter Title**: e.g., "Mastering WordPress Development"
3. **Add Content**: Full description of the ebook
4. **Set Featured Image**: This will be the cover image
5. **Add Excerpt**: Short preview text (used in cards and audio)

### Ebook Details Meta Box

Fill in the ebook metadata:

- **Price**: 29.99 (without currency symbol)
- **Stock**:
  - ✓ Unlimited Stock (digital, no limit)
  - OR Quantity: 100 (if limiting sales)
- **Author**: John Doe
- **Publisher**: Tech Publishing House
- **ISBN**: 978-1234567890
- **Pages**: 350
- **Language**: English

### Ebook File Upload

1. **Click**: "Choose File"
2. **Select**: PDF, EPUB, or DOCX (max 50MB)
3. **Upload**: File is stored securely in `/wp-content/uploads/skillscore-ebooks/`
4. **Security**: Direct access blocked via `.htaccess`

### Preview Settings

- **Enable Text Preview**: ✓ (shows excerpt on single page)
- **Enable Audio Preview**: ✓ (voice sample playback)
- **Preview Pages**: 3 (for text preview length)

### Publishing

Click **Publish** to make the ebook available for purchase.

---

## Shortcodes

### Display All Ebooks

```php
[skillscore_ebooks]
```

**With Parameters:**

```php
[skillscore_ebooks limit="12" category="fiction" orderby="date" order="DESC" columns="3"]
```

**Parameters:**
- `limit`: Number of ebooks (default: 12)
- `category`: Filter by category slug
- `orderby`: date, title, rand
- `order`: DESC, ASC
- `columns`: 1, 2, 3, or 4

### Display Single Ebook

```php
[skillscore_ebook id="123"]
```

Replace `123` with the actual ebook post ID.

### Where to Use Shortcodes

- **Pages**: Create a "Shop" page with `[skillscore_ebooks]`
- **Posts**: Add shortcodes in post content
- **Widgets**: Text widget with shortcode
- **Gutenberg**: Shortcode block
- **Classic Editor**: Directly in editor
- **Elementor**: Shortcode widget

---

## Elementor Integration

### Adding Ebook Widget

1. **Edit Page** with Elementor
2. **Search Widgets**: "SkillScore Ebooks"
3. **Drag & Drop** onto page
4. **Configure** in left panel:

#### Widget Settings

**Display Type:**
- **Grid**: Show multiple ebooks
- **Single**: Show one specific ebook

**Grid Settings (when Display Type = Grid):**
- Number of Ebooks: 12
- Columns: 3
- Category: All Categories
- Order By: Date
- Order: Descending

**Single Settings (when Display Type = Single):**
- Select Ebook: Choose from dropdown

5. **Update** page

### Styling with Elementor

The widget uses Tailwind CSS, but you can override:
- Go to: Style tab
- Customize colors, spacing, typography
- Changes apply to that instance only

---

## Payment Gateways

### Payment Flow

1. **Customer** fills purchase form (name, email, quantity, gateway)
2. **Plugin** validates stock and creates order (status: pending)
3. **Customer** redirected to payment gateway
4. **Payment** processed by gateway
5. **Webhook** notifies plugin of payment status
6. **Plugin** updates order (status: completed)
7. **Stock** decremented (if not unlimited)
8. **Download token** generated
9. **Customer** redirected to success page with download link

### Webhook URLs

For payment verification, provide these webhook URLs to payment gateways:

**Paystack Webhook:**
```
https://yoursite.com/?action=skillscore_payment_callback&gateway=paystack&order_id={ORDER_ID}
```

**Flutterwave Webhook:**
```
https://yoursite.com/?action=skillscore_payment_callback&gateway=flutterwave&order_id={ORDER_ID}
```

**Stripe Webhook:**
```
https://yoursite.com/?action=skillscore_payment_callback&gateway=stripe&order_id={ORDER_ID}
```

**PayPal IPN:**
```
https://yoursite.com/?action=skillscore_payment_callback&gateway=paypal&order_id={ORDER_ID}
```

### Testing Payments

**Paystack Test Cards:**
```
Card: 4084 0840 8408 4081
CVV: 408
Expiry: Any future date
PIN: 0000
```

**Flutterwave Test Cards:**
```
Card: 5531 8866 5214 2950
CVV: 564
Expiry: 09/32
OTP: 12345
```

**Stripe Test Cards:**
```
Success: 4242 4242 4242 4242
Decline: 4000 0000 0000 0002
CVV: Any 3 digits
Expiry: Any future date
```

**PayPal Sandbox:**
Use PayPal sandbox accounts created in developer dashboard.

---

## Audio Preview System

### Option 1: Global Voice Sample (Easiest)

**Best For**: Same voice across all ebooks

1. **Go to**: Ebooks → Settings → Voice Preview
2. **Enable Audio Preview**: ✓
3. **Use Global Voice Sample**: ✓
4. **Upload File**: MP3, WAV, or OGG (max 10MB)
5. **Save Changes**

This audio will play for ALL ebooks that have audio preview enabled.

### Option 2: Browser TTS (No Setup)

1. **TTS Engine**: Select "Browser TTS"
2. **Save Changes**

Uses browser's built-in speech synthesis (Web Speech API). No server setup needed, but voice quality varies by browser.

### Option 3: Piper TTS (Advanced)

**Best For**: High-quality, consistent voice generation

**Server Requirements:**
- Linux/Mac server
- Python 3.8+
- FFmpeg installed

**Installation:**

```bash
# Install Piper TTS
pip install piper-tts

# Download a voice model
wget https://github.com/rhasspy/piper/releases/download/v1.0.0/en_US-lessac-medium.tar.gz
tar -xzf en_US-lessac-medium.tar.gz -C ~/.local/share/piper/

# Install FFmpeg (if not already installed)
sudo apt-get install ffmpeg  # Ubuntu/Debian
```

**Configuration:**

1. **Go to**: Ebooks → Settings → Voice Preview
2. **TTS Engine**: Piper TTS
3. **Piper Executable Path**: `/usr/local/bin/piper`
4. **Piper Model**: `en_US-lessac-medium`
5. **FFmpeg Path**: `/usr/bin/ffmpeg`
6. **Save Changes**

**How It Works:**
- Extracts first 500 characters from ebook excerpt
- Generates WAV file using Piper
- Converts to MP3 using FFmpeg
- Caches audio in `/wp-content/uploads/skillscore-audio/`
- Serves cached version on subsequent requests

### Option 4: Coqui TTS (Advanced)

**Server Requirements:**
- Python 3.8+
- Coqui TTS server running

**Installation:**

```bash
# Install Coqui TTS
pip install TTS

# Run TTS server
tts-server --model_name tts_models/en/ljspeech/tacotron2-DDC
```

**Configuration:**

1. **TTS Engine**: Coqui TTS
2. **Coqui API URL**: `http://localhost:5002/api/tts`
3. **Save Changes**

### Clearing Audio Cache

To regenerate audio for an ebook:
1. Edit the ebook
2. Update excerpt
3. Audio will regenerate on next preview request

---

## Download Security

### Security Features

1. **Unique Tokens**: Each purchase gets a unique download token
2. **Download Limits**: Configurable (default: 5 downloads)
3. **Expiring Links**: Configurable (default: 30 days)
4. **IP Logging**: Tracks IP address and user agent
5. **Revokable**: Admin can revoke access anytime
6. **Protected Storage**: Files stored outside web root with `.htaccess` protection

### Download Process

**Customer Receives:**
```
https://yoursite.com/?skillscore_download=abc123xyz789...
```

**Plugin Validates:**
- ✓ Token exists in database
- ✓ Not revoked
- ✓ Not expired
- ✓ Download limit not exceeded

**Plugin Logs:**
- Download count incremented
- IP address recorded
- User agent recorded
- Timestamp recorded

**File Served:**
- Sets proper headers (Content-Disposition, etc.)
- Streams file (doesn't expose real path)
- Exit after delivery

### Managing Downloads

**View All Downloads:**
1. **Go to**: Ebooks → Downloads
2. **See**: Ebook, Customer, Download Count, Expiry, Status

**Revoke Access:**
1. Find the download record
2. Click "Revoke"
3. Customer can no longer download

---

## Troubleshooting

### "No valid plugins found"

**Cause**: Incorrect ZIP structure (double nesting)

**Solution:**
1. Extract ZIP and check structure
2. Should open directly to `skillscore-ebook-commerce/` folder
3. NOT: `skillscore-ebook-commerce/skillscore-ebook-commerce/`
4. Re-create ZIP correctly (see ZIP Structure section)

### Payment Not Processing

**Checklist:**
- ✓ Payment gateway enabled in settings?
- ✓ API keys correct (no extra spaces)?
- ✓ Using correct mode (test vs live keys)?
- ✓ Webhook URL configured in gateway dashboard?
- ✓ PHP errors in WordPress debug log?

**Debug:**
```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```
Check `/wp-content/debug.log` for errors.

### Download Link Not Working

**Checklist:**
- ✓ Order status is "completed"?
- ✓ Download token valid?
- ✓ Link not expired?
- ✓ Download limit not exceeded?
- ✓ File still exists in uploads folder?

**Reset Download:**
Admin can manually create a new download token from Orders page.

### Audio Preview Not Loading

**Browser TTS:**
- Check browser compatibility (Chrome, Edge, Safari best)
- Some browsers block autoplay

**Piper/Coqui TTS:**
- Verify executable path: `which piper`
- Check FFmpeg installed: `which ffmpeg`
- Check server permissions
- Test TTS manually: `echo "test" | piper --model en_US-lessac-medium --output_file test.wav`

### Styling Issues

**Tailwind CSS Not Loading:**
- Check browser console for CDN errors
- Verify CDN URL in plugin: `https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css`
- Try clearing browser cache

**Custom Styling:**
- Add custom CSS in: Appearance → Customize → Additional CSS
- Use `!important` to override Tailwind if needed

### File Upload Fails

**Checklist:**
- ✓ File size under 50MB?
- ✓ File type is PDF, EPUB, or DOCX?
- ✓ Server upload limit sufficient?
- ✓ `/wp-content/uploads/` writable?

**Increase Upload Limit:**

**In php.ini:**
```ini
upload_max_filesize = 64M
post_max_size = 64M
```

**In .htaccess:**
```apache
php_value upload_max_filesize 64M
php_value post_max_size 64M
```

**In wp-config.php:**
```php
@ini_set('upload_max_filesize', '64M');
@ini_set('post_max_size', '64M');
```

---

## Developer Guide

### Hooks & Filters

**Actions:**

```php
// Before payment initiation
do_action('skillscore_ebook_before_purchase', $ebook_id, $user_email);

// After successful payment
do_action('skillscore_ebook_after_purchase', $order_id, $ebook_id);

// When download starts
do_action('skillscore_ebook_download_started', $download_id, $ebook_id);

// When payment fails
do_action('skillscore_ebook_payment_failed', $order_id, $error_message);
```

**Filters:**

```php
// Modify ebook price
add_filter('skillscore_ebook_price', function($price, $ebook_id) {
    // Apply discount logic
    return $price * 0.9; // 10% off
}, 10, 2);

// Modify download expiry
add_filter('skillscore_ebook_download_expiry_days', function($days) {
    return 60; // Extend to 60 days
});

// Customize email content
add_filter('skillscore_ebook_purchase_email_body', function($body, $order) {
    // Customize email
    return $body;
}, 10, 2);
```

### Template Override

**Copy to Theme:**
```
your-theme/
└── skillscore-ebook/
    ├── ebook-card.php
    └── ebook-single.php
```

**Plugin checks:**
1. Theme directory first
2. Falls back to plugin templates

### Database Schema

**Orders Table:**
```sql
wp_skillscore_orders
- id (bigint)
- order_reference (varchar)
- ebook_id (bigint)
- user_email (varchar)
- user_name (varchar)
- user_id (bigint, nullable)
- quantity (int)
- amount (decimal)
- currency (varchar)
- payment_gateway (varchar)
- payment_status (varchar)
- transaction_id (varchar, nullable)
- order_date (datetime)
```

**Downloads Table:**
```sql
wp_skillscore_downloads
- id (bigint)
- order_id (bigint)
- ebook_id (bigint)
- download_token (varchar, unique)
- download_count (int)
- download_limit (int)
- expires_at (datetime, nullable)
- created_at (datetime)
- last_downloaded_at (datetime, nullable)
- ip_address (varchar, nullable)
- user_agent (text, nullable)
- is_revoked (tinyint)
```

### Custom Payment Gateway

**Create Gateway Class:**

```php
class My_Custom_Gateway {

    public function initiate_payment($order_id, $amount, $currency) {
        // 1. Create payment request to gateway API
        // 2. Return redirect URL
        return [
            'success' => true,
            'data' => [
                'redirect_url' => 'https://gateway.com/pay?ref=123'
            ]
        ];
    }

    public function verify_payment($order) {
        // 1. Verify payment with gateway API
        // 2. Return true if successful
        return true;
    }
}
```

**Register Gateway:**

```php
add_filter('skillscore_ebook_payment_gateways', function($gateways) {
    $gateways['my_gateway'] = new My_Custom_Gateway();
    return $gateways;
});
```

---

## FAQ

### Can I offer free ebooks?

Yes, set price to `0.00`. Download link generated immediately without payment.

### Can I set different prices for different regions?

Use a filter:
```php
add_filter('skillscore_ebook_price', function($price, $ebook_id) {
    $user_country = get_user_country(); // Your logic
    if ($user_country === 'NG') {
        return $price * 0.7; // 30% off for Nigeria
    }
    return $price;
}, 10, 2);
```

### Can customers download after expiry?

No. Download links are strictly enforced. Admin can create a new download token manually if needed.

### Does it support variable pricing?

Not by default, but can be implemented via filters and custom code.

### Can I bulk import ebooks?

Not built-in, but you can use WP All Import plugin with custom fields mapping.

### Is it multisite compatible?

Yes, but activate per site (not network-wide recommended).

### Can I translate the plugin?

Yes, it's translation-ready. Use Loco Translate plugin or create `.po` files.

### Does it work with membership plugins?

Yes, you can restrict ebook access using membership plugins like MemberPress or Restrict Content Pro.

### Can I export orders?

Use "Export to CSV" plugins like WP All Export, or query the database directly.

### How do I backup ebook files?

Include `/wp-content/uploads/skillscore-ebooks/` in your backup routine. Most backup plugins (UpdraftPlus, BackWPup) do this automatically.

---

## Support & Credits

**Plugin**: SkillScore Ebook Commerce
**Author**: SkillScore IT Solutions and Training
**Developer**: Tijani Bulama
**Version**: 1.0.0
**License**: GPL-2.0+

For questions, feature requests, or support:
- Contact SkillScore IT Solutions and Training
- Developer: Tijani Bulama

---

**Last Updated**: 2025
**Documentation Version**: 1.0.0
