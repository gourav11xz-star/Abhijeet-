sudo mkdir -p /var/www/html/abhijeet/public/css

sudo tee /var/www/html/abhijeet/public/css/premium-categories.css > /dev/null <<'EOF'
/* Premium Category Cards */
.category-card,
a[href*="category="] {
    position: relative;
    border-radius: 24px !important;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 1px solid rgba(99, 102, 241, 0.10) !important;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06) !important;
    transition: all 0.28s ease !important;
    overflow: hidden;
}

.category-card:hover,
a[href*="category="]:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 22px 45px rgba(79, 70, 229, 0.18) !important;
    border-color: rgba(79, 70, 229, 0.35) !important;
}

.category-card::before,
a[href*="category="]::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(99,102,241,0.14), transparent 40%);
    opacity: 0;
    transition: opacity 0.28s ease;
}

.category-card:hover::before,
a[href*="category="]:hover::before {
    opacity: 1;
}

.category-card img,
a[href*="category="] img {
    transition: transform 0.28s ease, filter 0.28s ease;
}

.category-card:hover img,
a[href*="category="]:hover img {
    transform: scale(1.12);
    filter: drop-shadow(0 10px 14px rgba(79,70,229,0.25));
}

/* Letter circle improvement */
.category-card span,
a[href*="category="] span {
    transition: all 0.28s ease;
}

.category-card:hover span,
a[href*="category="]:hover span {
    transform: scale(1.08);
}
EOF

sudo python3 - <<'PY'
from pathlib import Path

p = Path("/var/www/html/abhijeet/app/views/inc/header.php")
s = p.read_text()

link = '<link rel="stylesheet" href="<?= URL_ROOT ?>/css/premium-categories.css">'

if "premium-categories.csudo sed -i '/^EOF$/d' /var/www/html/abhijeet/public/css/premium-categories.css
php -l /var/www/html/abhijeet/app/views/inc/header.php
sudo systemctl restart apache2

