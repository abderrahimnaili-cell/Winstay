 # WINSTAY

Technical Architecture & Specification Document

> Architecture: Three-Tier Software Architecture
> Project Repository: [https://github.com/your-username/winstay](https://github.com/abderrahimnaili-cell/Winstay)

---

## 1. Project Abstract

WINSTAY is a comprehensive, full-stack web application designed to digitize and streamline the property reservation process. Emulating industry-standard booking platforms, the system enables users to browse available properties, view detailed listings with interactive maps, and complete reservations through a secure, authenticated workflow. 

The project demonstrates a clear understanding of Three-Tier Software Architecture, separating the presentation layer (Frontend), business logic layer (Backend), and data persistence layer (Database). The primary objective is to deliver a scalable, maintainable, and user-centric solution that addresses real-world challenges in property management and booking.

## 2. System Architecture & Technical Stack

The application is engineered using a modular, layered architecture to ensure separation of concerns, ease of maintenance, and future scalability.

### 2.1 Presentation Layer (Frontend)

Technologies: HTML5, CSS3, JavaScript (Vanilla).

Components:
- index.php: Main landing page featuring property listings, an interactive map, and a "Book Now" call-to-action.
- admin.php: A VIP administrative dashboard allowing managers to approve/reject reservation requests and view user statistics.
- help_center.php: A support page for visitors unfamiliar with the platform.
- CSS Directory (/css): Premium styling files (colors, typography, layout) for a polished user interface.
- JavaScript Directory (/js): Handles client-side interactivity (e.g., the "Book Now" button triggers asynchronous requests to the backend without page reload).

### 2.2 Business Logic Layer (Backend)

Technologies: PHP.

- API Endpoints (/api): Dedicated routes for handling requests: api/auth (User authentication and login), api/properties (Retrieval of property data), api/reservations (Creation and management of booking requests).
- Middleware (/middleware): The auth.php file acts as a security gatekeeper, verifying user identity and session validity before granting access to sensitive operations.
- Configuration (/config): Centralized database connection settings (database.php) for secure and maintainable credential management.
- Utilities (/utils): Helper functions, including OTP (One-Time Password) generation for enhanced security.

### 2.3 Data Persistence Layer (Database)

Technologies: MySQL.

Schema Design: The database is initialized via install.sql and includes the following core tables:
- Utilisateurs: Stores user account information (names, emails, passwords).
- Logements: Contains property details (price, location, map coordinates).
- DemandesReservation: Manages the lifecycle of booking requests (user, property, dates).
- Images: Stores URLs for property images.

## 3. Functional Workflow: The Booking Lifecycle

The following scenario illustrates the end-to-end integration of the system's layers:

1. User Interaction (Frontend): A user (e.g., "Amine") browses the main page (index.php) and clicks "Book Now" on a desired property.
2. Authentication (Middleware): JavaScript sends a request to the backend. The middleware/auth.php verifies whether Amine is logged in. If not, he is redirected to the login page.
3. Request Processing (API): Upon successful authentication, the request is routed to api/reservations/create.php, which validates the data and communicates with the database.
4. Data Persistence (Database): A new reservation record is inserted into the DemandesReservation table (e.g., "Amine reserves Property #5 for Friday").
5. Response (Frontend): The system returns a confirmation message to the user: "Reservation Successful!" - completing a full-cycle interaction.

## 4. Key Technical Highlights & Value Proposition
 - Security: Middleware-based authentication prevents unauthorized access to sensitive data and operations.
- Administrative Control: A dedicated admin dashboard (admin.php) enables property managers to oversee reservations, manage listings, and access usage statistics.
- Scalability: The modular architecture allows for seamless integration of future features (e.g., online payment gateways) without disrupting existing functionality.
- User Support: The inclusion of a help center reflects a commitment to an inclusive and user-friendly experience.

## 5. Conclusion

WINSTAY represents a complete, functional, and well-architected web application that demonstrates proficiency in full-stack development, database design, and secure system integration. The project reflects a strong foundation in software engineering principles and a clear ability to translate real-world requirements into scalable technical solutions.
