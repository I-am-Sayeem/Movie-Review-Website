# CineVault — Figma Design Specification

## 📋 Project Overview
**Project Name:** CineVault — Movie Review Platform  
**Tech Stack:** PHP, MySQL, HTML/CSS/JS  
**Design Style:** Dark Glassmorphism with Light Mode  
**Fonts:** Inter (body), Outfit (headings)  
**Resolution:** 1440×900 (Desktop), 768px (Tablet), 480px (Mobile)

---

## 🎨 Design System / Tokens

### Color Palette

#### Dark Mode (Default)
| Token | Value | Usage |
|---|---|---|
| bg-primary | `#0a0a1a` | Page background |
| bg-secondary | `#0f0f2a` | Cards, footer background |
| bg-tertiary | `#151535` | Elevated surfaces |
| surface | `rgba(255,255,255,0.04)` | Input backgrounds |
| surface-hover | `rgba(255,255,255,0.08)` | Hover states |
| surface-card | `rgba(255,255,255,0.06)` | Card backgrounds |
| glass | `rgba(255,255,255,0.05)` | Glassmorphism fills |
| glass-border | `rgba(255,255,255,0.1)` | Glassmorphism borders |
| primary | `#6c5ce7` | Primary purple |
| primary-light | `#a29bfe` | Light purple (accents) |
| primary-dark | `#4834d4` | Dark purple (gradients) |
| secondary | `#00cec9` | Teal accent |
| secondary-light | `#55efc4` | Light teal |
| accent-warm | `#fd79a8` | Pink accent |
| star-color | `#ffd700` | Gold star ratings |
| text-primary | `#e8e8f0` | Main text |
| text-secondary | `#8888aa` | Subtitle text |
| text-muted | `#555577` | Muted/helper text |
| success | `#00b894` | Success messages |
| error | `#ff6b6b` | Error messages |
| warning | `#fdcb6e` | Warning messages |

#### Light Mode
| Token | Value | Usage |
|---|---|---|
| bg-primary | `#f0f2f5` | Page background |
| bg-secondary | `#e4e6eb` | Cards background |
| bg-tertiary | `#d8dbe2` | Elevated surfaces |
| surface-card | `rgba(255,255,255,0.85)` | Card backgrounds |
| glass | `rgba(255,255,255,0.6)` | Glassmorphism fills |
| glass-border | `rgba(0,0,0,0.1)` | Glassmorphism borders |
| text-primary | `#1a1a2e` | Main text |
| text-secondary | `#555577` | Subtitle text |
| text-muted | `#8888aa` | Muted text |

### Typography
| Style | Font | Size | Weight |
|---|---|---|---|
| Hero H1 | Outfit | 3.8rem / 60.8px | 800 |
| Page H1 | Outfit | 2.4rem / 38.4px | 800 |
| Section H2 | Outfit | 2.2rem / 35.2px | 700 |
| Card Title | Outfit | 1.05rem / 16.8px | 700 |
| Body | Inter | 1rem / 16px | 400 |
| Small | Inter | 0.85rem / 13.6px | 400 |
| Tag/Label | Inter | 0.72rem / 11.5px | 600, uppercase |

### Border Radius
| Token | Value |
|---|---|
| radius-sm | 8px |
| radius-md | 12px |
| radius-lg | 16px |
| radius-xl | 24px |
| radius-full | 50% (circle) |

### Shadows
| Token | Value |
|---|---|
| shadow-sm | `0 2px 8px rgba(0,0,0,0.3)` |
| shadow-md | `0 8px 32px rgba(0,0,0,0.4)` |
| shadow-lg | `0 16px 48px rgba(0,0,0,0.5)` |
| shadow-glow | `0 0 40px rgba(108,92,231,0.15)` |

### Spacing
- Container max-width: 1200px
- Container padding: 0 20px (desktop), 0 40px (navbar)
- Section padding: 80px 0
- Card padding: 16px–48px
- Navbar height: 70px

---

## 📐 Component Library

### 1. Navbar (Fixed, 70px height)
- **Background:** `rgba(10,10,26,0.85)` + `backdrop-filter: blur(20px)`
- **Border-bottom:** `1px solid rgba(255,255,255,0.08)`
- **Layout:** CSS Grid `1fr auto 1fr` with 32px gap, padding 0 40px
- **Logo:** 🎬 emoji (1.6rem) + "Cine" (white) + "Vault" (gradient purple→teal), Outfit 1.5rem 800
- **Nav Links:** Inter 0.92rem 500, color `#8888aa`, hover: white + `rgba(255,255,255,0.08)` bg, Active: `#a29bfe` + `rgba(108,92,231,0.12)` bg, padding 8px 16px, radius 8px
- **Nav Actions:** Theme toggle button (40x40, radius 8px), User avatar (34x34 circle, gradient bg), Username, Logout/Login buttons
- **Scrolled state:** `rgba(10,10,26,0.95)` + box-shadow

### 2. Buttons
| Variant | Background | Color | Shadow |
|---|---|---|---|
| Primary | `linear-gradient(135deg, #6c5ce7, #4834d4)` | White | `0 4px 15px rgba(108,92,231,0.3)` |
| Secondary | `linear-gradient(135deg, #00cec9, #00b5b0)` | White | `0 4px 15px rgba(0,206,201,0.3)` |
| Outline | Transparent | White | — |
| Danger | `linear-gradient(135deg, #ff6b6b, #e74c3c)` | White | — |

- **Padding:** 12px 28px (default), 8px 18px (sm), 16px 36px (lg)
- **Radius:** 12px (default), 16px (lg)
- **Hover:** translateY(-2px) + increased shadow

### 3. Movie Card
- **Background:** `rgba(255,255,255,0.06)` + blur(20px)
- **Border:** `1px solid rgba(255,255,255,0.08)`
- **Radius:** 16px
- **Poster area:** 2:3 aspect ratio, rounded top corners
- **Placeholder:** Gradient `#151535 → #0f0f2a`, film emoji (3rem, 40% opacity), title text
- **Card body:** 16px padding, title (Outfit 1.05rem 700), meta row with genre tag + year, rating row with stars + review count
- **Hover:** translateY(-4px), border-color lighten, shadow-md

### 4. Genre Tag
- Padding: 4px 10px
- Radius: 20px (pill)
- Font: 0.72rem, 600 weight, uppercase, 0.5px letter-spacing
- Background: `rgba(108,92,231,0.15)`
- Color: `#a29bfe`
- Border: `1px solid rgba(108,92,231,0.2)`

### 5. Star Rating
- Size: 1rem per star (display), 2rem per star (interactive)
- Filled: color `#ffd700`, text-shadow `0 0 8px rgba(255,215,0,0.3)`
- Empty: `rgba(255,215,0,0.2)`
- Rating number: 0.9rem, 700 weight, color `#ffd700`

### 6. Form Card (Glassmorphism)
- Max-width: 440px (default), 640px (wide)
- Padding: 48px 40px
- Background: `rgba(255,255,255,0.03)` + blur(24px)
- Border: `1px solid rgba(255,255,255,0.08)`, top/left borders `rgba(255,255,255,0.15)`
- Radius: 24px
- Shadow: `0 16px 40px rgba(0,0,0,0.4), inset 0 0 0 1px rgba(255,255,255,0.05)`
- Animated shine sweep effect across card

### 7. Form Input
- Padding: 14px 16px
- Background: `rgba(255,255,255,0.04)`
- Border: `1.5px solid rgba(255,255,255,0.08)`
- Radius: 12px
- Focus: border `#6c5ce7`, box-shadow `0 0 0 4px rgba(108,92,231,0.12)`

### 8. Review Card
- Padding: 24px
- Avatar: 44x44 circle, gradient purple→teal, white initials (0.85rem, 700)
- Username: 0.95rem, 700 weight
- Date: 0.8rem, muted color
- Content: 0.95rem, text-secondary, line-height 1.7
- Actions: Edit (outline btn-sm) + Delete (danger btn-sm)

### 9. Footer
- Background: `#0f0f2a`, border-top: `1px solid rgba(255,255,255,0.08)`
- Padding: 60px 0 30px
- Grid: `1.5fr 1fr 1fr` with 40px gap
- Links: 0.9rem, muted color, hover purple-light
- Bottom: flex space-between, 0.85rem, muted

### 10. Flash Message
- Position: fixed, top 80px, centered
- Padding: 14px 24px, radius 12px
- backdrop-filter: blur(20px)
- Success: green tones, Error: red tones
- Slide-down animation, auto-dismiss 4s

### 11. Modal (Delete Confirmation)
- Overlay: `rgba(0,0,0,0.7)` + blur(8px)
- Modal card: bg-secondary, border, radius 16px, padding 32px, max-width 420px
- Scale-in animation
- Cancel + Delete buttons centered

---

## 📄 Pages (8 Total)

### Page 1: Homepage (`index.php`)
**File:** `01_Homepage.png`
- Hero section with large heading, subtitle, search bar, stats
- Trending movies grid (6 cards, 2:3 ratio)
- Latest reviews grid (3 columns)
- Footer

### Page 2: Login (`login.php`)
**File:** `02_Login.png`
- Centered glassmorphism form card
- Email + Password fields
- "Log In" button (primary, full-width, large)
- Link to register page
- Purple glow aura behind card

### Page 3: Register (`register.php`)
**File:** `03_Register.png`
- Centered glassmorphism form card
- Username + Email + Password + Confirm Password fields
- "Sign Up" button (primary, full-width, large)
- Link to login page

### Page 4: Browse Movies (`movies.php`)
**File:** `04_Browse_Movies.png`
- Page header with title + "Add Movie" button
- Filter bar: search + 3 dropdowns
- Movie grid (4 columns of movie cards)
- Pagination controls

### Page 5: Movie Detail (`movie.php`)
**File:** `05_Movie_Detail.png`
- Two-column layout: poster (320px) + info
- Movie title, meta tags, large rating display
- Description text
- Reviews list (cards)
- "Write a Review" form (stars + textarea)
- Delete confirmation modal

### Page 6: User Profile (`profile.php`)
**File:** `06_Profile.png`
- Profile header card with avatar, info, stats
- Edit profile form card
- User's reviews list
- Delete modal

### Page 7: Add Movie (`add_movie.php`)
**File:** `07_Add_Movie.png`
- Page header with title
- Wide form card with:
  - Title input
  - Genre dropdown + Year input (2-column)
  - Description textarea
  - File upload drop zone
  - "Add Movie" button

### Page 8: Edit Review (`edit_review.php`)
**File:** `08_Edit_Review.png`
- Page header with title
- Wide form card with:
  - Movie title link
  - Interactive star rating
  - Review textarea
  - "Update Review" + "Cancel" buttons

---

## 🔄 Interactions & Animations

### Hover Effects
- Movie cards: translateY(-4px), shadow increase, poster scale 1.05
- Buttons: translateY(-2px), shadow glow increase
- Nav links: bg surface-hover, text color lighten
- Form card: translateY(-5px), purple glow shadow

### Animations
- `fadeInUp`: opacity 0→1, translateY(20px→0), 0.5s
- `slideDown`: flash message entrance from top
- `pulseGlow`: auth page background glow pulsing 8s
- `shineCard`: shimmer sweep across form cards 6s
- `modalIn`: scale(0.9→1) + opacity, 0.3s
- Counter animation: numbers count up from 0, 1.5s
- Theme transition: 0.4s ease on background/color/border

### Responsive Breakpoints
- **1024px:** Movie detail grid narrows, profile card stacks
- **768px:** Mobile nav (hamburger), single column layouts, reduced fonts
- **480px:** Extra compact, hero stats stack, smaller cards

---

## 📦 How to Import into Figma

### Method 1: Image Frames (Quickest)
1. Open Figma → Create new file
2. Create a page for each screen (8 pages)
3. Drag each PNG from the `figma-export` folder onto its page
4. Rename pages: Homepage, Login, Register, Browse Movies, Movie Detail, Profile, Add Movie, Edit Review

### Method 2: Rebuild with Design Tokens (Best for editing)
1. Create a new Figma file
2. Set up **Local Styles** using the color palette above
3. Set up **Text Styles** using the typography table
4. Create **Components** for: Navbar, Button variants, Movie Card, Review Card, Star Rating, Form Input, Genre Tag, Footer
5. Build each page frame (1440×900) using these components
6. Use the PNG mockups as visual reference overlays

### Method 3: Use html.to.design Plugin
1. Install the **"html.to.design"** Figma plugin
2. Run your CineVault site locally (`php -S localhost:8000`)
3. Use the plugin to import each page URL directly into Figma as editable layers

### Recommended Figma Structure
```
📁 CineVault
├── 📄 Cover
├── 📄 Design System (Colors, Typography, Components)
├── 📄 01 — Homepage
├── 📄 02 — Login
├── 📄 03 — Register
├── 📄 04 — Browse Movies
├── 📄 05 — Movie Detail
├── 📄 06 — Profile
├── 📄 07 — Add Movie
└── 📄 08 — Edit Review
```
