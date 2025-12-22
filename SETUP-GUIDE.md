# SkillScore Ebook Commerce - Complete Setup Guide

## Quick Start (5 Minutes)

### Step 1: Install the Plugin
1. Go to **WordPress Admin → Plugins → Add New**
2. Click **Upload Plugin**
3. Choose `skillscore-ebook-commerce.zip`
4. Click **Install Now**, then **Activate**

### Step 2: Generate Sample Ebooks
1. Go to **Ebooks → Generate Samples**
2. Click **"Generate 5 Sample Ebooks"**
3. You'll see 5 professional sample ebooks created instantly

### Step 3: Test the Display
1. Create a new **Page** or **Post**
2. Add the shortcode: `[skillscore_ebooks]`
3. **Publish** and view the page
4. You should see 5 ebooks in a beautiful yellow/black grid

---

## Common Issues & Solutions

### Issue 1: "Interface is Still Not Improved" or "CSS Not Loading"

**Symptoms:**
- Ebooks display in plain text without styling
- No yellow/black theme visible
- Grid layout not working

**Solutions:**

#### A. Clear ALL Caches
1. **WordPress Cache**: If using W3 Total Cache, WP Super Cache, etc., clear the cache
2. **CDN Cache**: If using Cloudflare or similar, purge the cache
3. **Browser Cache**: Hard refresh with `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)
4. **Theme Cache**: Some themes cache shortcode output - check theme settings

#### B. Check Shortcode Usage
Make sure you're using the shortcode correctly:
```
[skillscore_ebooks]              ← Display all ebooks in grid
[skillscore_ebooks columns="3"]  ← Display in 3 columns
[skillscore_ebook id="123"]      ← Display single ebook (replace 123 with actual ID)
```

#### C. Verify Assets are Loading
1. Right-click on the page → **Inspect** → **Console tab**
2. Look for any red errors related to CSS or JS files
3. If you see 404 errors, the plugin URL might be wrong

#### D. Check for Plugin Conflicts
1. Temporarily deactivate other plugins
2. Test if the styling appears
3. Reactivate plugins one by one to find conflicts

---

### Issue 2: "Audio Preview Not Showing"

**Symptoms:**
- Audio preview section missing
- Audio button doesn't appear
- No audio player visible

**Required Configuration:**

#### Step 1: Enable Audio Preview Globally
1. Go to **Ebooks → Settings**
2. Click **"Voice Preview" tab**
3. **Upload a global voice sample** (MP3/WAV/OGG file) OR
4. **Configure TTS settings** (Piper/Coqui TTS) OR
5. **Enable Browser TTS** (uses Web Speech API - free, no setup)

#### Step 2: Enable Audio for Specific Ebooks
1. Go to **Ebooks → All Ebooks**
2. Edit an ebook
3. Find **"Preview Settings"** meta box
4. Check **"Enable Audio Preview"**
5. Click **Update**

#### Step 3: Verify JavaScript is Loading
1. View the ebook page
2. Right-click → **Inspect** → **Console tab**
3. Type: `typeof skillscoreEbook`
4. Should return `"object"` (not `"undefined"`)

---

### Issue 3: Deprecated Function Warning

**Warning Message:**
```
Deprecated: Function get_page_by_title is deprecated since version 6.2.0!
Use WP_Query instead.
```

**Status:** ✅ **FIXED** in latest version

The sample generator now uses `WP_Query` instead of the deprecated `get_page_by_title()` function. Update to the latest version to remove this warning.

---

### Issue 4: "Ebooks Not Displaying in Grid"

**Symptoms:**
- Ebooks display vertically (one per row) instead of in a grid
- No responsive multi-column layout

**Solutions:**

#### A. Check Column Parameter
```
[skillscore_ebooks columns="3"]  ← Force 3 columns
[skillscore_ebooks columns="4"]  ← Force 4 columns
```

#### B. Verify Responsive CSS Classes
The plugin uses these responsive breakpoints:
- **Mobile** (< 768px): 1 column
- **Tablet** (768px - 1023px): 2-3 columns
- **Desktop** (1024px+): 3-4 columns

Test on different screen sizes to ensure it works.

#### C. Check for CSS Conflicts
Some themes override grid styles. Add this to your theme's `style.css`:
```css
.skillscore-ebooks-grid.grid {
    display: grid !important;
}
```

---

## Advanced Configuration

### Payment Gateway Setup

#### Paystack Setup
1. Go to **Ebooks → Settings → Payment Gateways**
2. Check **"Enable Paystack"**
3. Enter **Secret Key** from [Paystack Dashboard](https://dashboard.paystack.com/#/settings/developer)
4. Enter **Public Key**
5. Select **Currency** (NGN for Nigerian Naira)
6. Save changes

#### Flutterwave Setup
1. Check **"Enable Flutterwave"**
2. Enter **Secret Key** from [Flutterwave Dashboard](https://dashboard.flutterwave.com/settings/apis)
3. Enter **Public Key**
4. Enter **Encryption Key**
5. Save changes

#### Stripe Setup
1. Check **"Enable Stripe"**
2. Enter **Secret Key** from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)
3. Enter **Publishable Key**
4. Save changes

#### PayPal Setup
1. Check **"Enable PayPal"**
2. Enter **Client ID** from [PayPal Developer](https://developer.paypal.com/developer/applications)
3. Enter **Client Secret**
4. Select **Mode** (Sandbox for testing, Live for production)
5. Save changes

---

### Customizing Ebook Display

#### Grid Layout Options
```
[skillscore_ebooks limit="12" columns="3" orderby="date" order="DESC"]
```

**Parameters:**
- `limit` - Number of ebooks to display (default: 12)
- `columns` - Number of columns (1-4, default: 3)
- `category` - Filter by category slug (e.g., `category="business"`)
- `orderby` - Sort by: date, title, price (default: date)
- `order` - ASC or DESC (default: DESC)

#### Single Ebook Display
```
[skillscore_ebook id="123"]
```

Replace `123` with the actual ebook post ID.

---

### Customizing Colors

The plugin uses CSS variables for easy customization. Add this to your theme's `style.css`:

```css
:root {
    --neon-yellow: #FFE500;      /* Change to your brand color */
    --rich-black: #0D0D0D;       /* Background color */
    --dark-gray: #1A1A1A;        /* Card backgrounds */
    --light-gray: #2A2A2A;       /* Borders */
}
```

---

### Adding Real Book Covers

1. Go to **Ebooks → All Ebooks**
2. Edit an ebook
3. In the right sidebar, find **"Featured Image"**
4. Click **"Set featured image"**
5. Upload your book cover (recommended size: 600x800px, 3:4 ratio)
6. Click **"Set featured image"**
7. Click **"Update"**

**Cover Image Best Practices:**
- Format: JPG or PNG
- Size: 600x800px (3:4 aspect ratio)
- File size: < 500KB
- High quality but optimized

---

### Uploading Ebook Files

1. Edit an ebook
2. Find **"File Upload"** meta box
3. Click **"Choose File"**
4. Select your PDF/EPUB file
5. Click **"Upload Ebook File"**
6. Click **"Update"**

**Supported formats:**
- PDF (.pdf) - Most common
- EPUB (.epub) - E-reader format
- DOCX (.docx) - Microsoft Word

**File size limit:** 50MB (can be increased in server settings)

---

## Troubleshooting Checklist

### Before Asking for Help, Check:

- [ ] Plugin is activated
- [ ] All caches cleared (WordPress + Browser + CDN)
- [ ] Shortcode is on the page and correctly formatted
- [ ] Sample ebooks have been generated
- [ ] At least one ebook has a featured image
- [ ] No JavaScript errors in browser console (F12)
- [ ] CSS file is loading (check Network tab in browser dev tools)
- [ ] PHP version is 7.4 or higher
- [ ] WordPress version is 6.0 or higher

### Still Not Working?

1. **Check PHP Error Log**: Go to **Tools → Site Health → Info → Server**
2. **Test with Default Theme**: Switch to Twenty Twenty-Three theme temporarily
3. **Disable Other Plugins**: Test with only SkillScore Ebook Commerce active
4. **Check File Permissions**: Ensure `/wp-content/uploads/` is writable

---

## Sample Ebook Details

When you generate samples, you'll get these 5 professional ebooks:

| Title | Author | Category | Price | Pages |
|-------|--------|----------|-------|-------|
| No Excuses, No Miracles | Duke Ofotare | Self-Help | $24.99 | 256 |
| The Digital Marketing Blueprint | Sarah Johnson | Business | $39.99 | 342 |
| Python for Data Science | Dr. Michael Chen | Technology | $44.99 | 418 |
| The Minimalist Entrepreneur | James Martinez | Business | $29.99 | 298 |
| Atomic Habits for Creative Minds | Emma Williams | Self-Help | $34.99 | 276 |

**What's Included in Each Sample:**
- ✅ Complete title, author, and description
- ✅ Professional excerpt for previews
- ✅ ISBN, publisher, page count, language
- ✅ Category assignment
- ✅ Audio preview enabled (where applicable)
- ✅ Unlimited stock enabled
- ⚠️ No book cover image (you need to add these)
- ⚠️ No actual ebook file (you need to upload these)

---

## Next Steps After Setup

1. **Add Book Covers** to all 5 sample ebooks
2. **Configure Payment Gateways** in Settings
3. **Test a Purchase** using Sandbox/Test mode
4. **Customize Colors** to match your brand
5. **Create Your Own Ebooks** using the sample format
6. **Set Up Email Notifications** (recommended)
7. **Configure Voice Preview** for audio samples
8. **Test on Mobile Devices** to ensure responsiveness

---

## Support & Resources

- **Plugin Version**: 1.0.0
- **Minimum WordPress**: 6.0
- **Minimum PHP**: 7.4
- **License**: GPL v2 or later

### Shortcode Quick Reference

```
[skillscore_ebooks]                           ← All ebooks, 3 columns
[skillscore_ebooks limit="6"]                 ← Show 6 ebooks
[skillscore_ebooks columns="4"]               ← 4 column grid
[skillscore_ebooks category="business"]       ← Business category only
[skillscore_ebooks orderby="price" order="ASC"] ← Cheapest first
[skillscore_ebook id="123"]                   ← Single ebook view
```

---

## Performance Tips

1. **Enable Caching**: Use a caching plugin (W3 Total Cache, WP Rocket)
2. **Optimize Images**: Use WebP format for book covers
3. **Use CDN**: Cloudflare or similar for faster asset delivery
4. **Lazy Loading**: Enable lazy loading for images
5. **Minify CSS/JS**: Use Autoptimize or similar
6. **Database Optimization**: Use WP-Optimize to clean up database

---

## Security Best Practices

1. **Keep Plugin Updated**: Always use the latest version
2. **Use SSL**: Ensure your site has HTTPS enabled
3. **Strong API Keys**: Keep payment gateway keys secure
4. **Regular Backups**: Backup your site and database regularly
5. **File Permissions**: Ensure proper file permissions (644 for files, 755 for directories)
6. **Monitor Downloads**: Check download logs regularly for suspicious activity

---

## FAQ

### Q: Can I sell physical books with this plugin?
**A:** This plugin is designed for digital ebooks only. For physical products, use WooCommerce.

### Q: Can I offer free ebooks?
**A:** Yes! Set the price to $0.00 and users can "purchase" for free.

### Q: Can I limit the number of downloads per purchase?
**A:** Yes, this is configured in the download handler. Default is 3 downloads per purchase.

### Q: How long are download links valid?
**A:** Download links expire after 48 hours by default. This can be changed in the code.

### Q: Can I use this with Elementor?
**A:** Yes! The plugin includes an Elementor widget. Go to Elementor → Add Widget → Search for "SkillScore Ebook"

### Q: Does this work with Gutenberg?
**A:** Yes! Use the shortcode block and paste the shortcode.

### Q: Can I translate the plugin?
**A:** Yes! The plugin is translation-ready. Use Loco Translate or similar.

---

**Last Updated**: December 22, 2025
**Version**: 1.0.0
**Author**: SkillScore IT Solutions and Training
**Developer**: Tijani Bulama
