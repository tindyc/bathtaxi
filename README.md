# Taxi Website – Bath, UK

This website was designed and developed for a private client who operates a taxi service in the Bath region.  
The design, layout, and features were created to reflect the client’s logo, branding, and personal preferences, ensuring the site matches his style and professional image.

**Live Links:**
- 🌐 [Production Website](https://acetoursandtransfers.co.uk/)
- 🔍 [GitHub Pages Preview](https://tindyc.github.io/acetoursandtransfers/)

---

## Design & Branding
- The site’s colour palette and styles were tailored to match the client’s **logo** and brand identity.
- Includes **photographs of the client’s vehicles** taken in and around the Bath area, giving the site a local and authentic feel.
- Fully **responsive design** for mobile, tablet, and desktop users.
- **Preloader Animation**:
  - On first page load, a V-Class vehicle (matching the client’s actual fleet) drives across the screen on a stylised road.
  - Once the vehicle reaches the end of its journey, it disappears and the client’s logo is revealed in the center.
  - This animation is designed to create a polished, high-quality first impression before the main content is displayed.

---

## Features
- **Contact Form** – allows potential customers to send enquiries directly from the site.
- **Email Integration (PHP & PHPMailer)** – the form sends enquiry details securely to the client via email for quick response.
- **Direct WhatsApp Contact** – visitors can click a WhatsApp link to instantly start a chat with the client for quicker responses.
- **Image Galleries** – showcase the client’s fleet and services.
- **Privacy Policy Page** – details how customer data is collected, used, and stored in line with UK GDPR.
- **Local Focus** – text and imagery reflect the Bath region and surrounding areas.

---

## Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript (with custom preloader animation)
- **Backend**: PHP (for form handling and email sending)
- **Libraries**: Bootstrap, Font Awesome, PHPMailer
- **Hosting**: Hostinger
- **Domain Management**: Domain name is registered and managed on behalf of the client.

---

## Getting Started

### 1) Environment File (`.env`)
This project requires an `.env` file in the root directory for email functionality (PHPMailer SMTP) to work.

**Example `.env` file:**
```env
SMTP_HOST=smtp.hostinger.com
SMTP_USER=booking@acetoursandtransfers.co.uk
SMTP_PASS=PASSWORD_HERE
FROM_EMAIL=booking@acetoursandtransfers.co.uk
FROM_NAME="Ace Tours & Transfers"
OWNER_TO=booking@acetoursandtransfers.co.uk
```

### 2) Importance of the vendor/ Folder
The vendor/ folder is critical. It contains Composer dependencies, including PHPMailer, used to send emails from the booking form.

If vendor/ is missing:

The contact form will fail to send emails.

PHP will throw “class not found” errors.

Tip: Never delete vendor/ when deploying. If it’s missing, run:
```
composer install
```

### Hosting & Management
Hosted on Hostinger.

Domain registration and DNS managed for reliability.

Integrated with GitHub for easy deployment of updates.

### Usage
Visitors can:

View the animated preloader on first load.

Learn more about the taxi service.

View photos of vehicles.

Submit an enquiry through the contact form.

Read the Privacy Policy to understand how their data is handled.

### Notes
This project was built for a specific client and is not intended for resale or redistribution without permission.
All images, branding elements, and the domain are the property of the client.

