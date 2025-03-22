# 📞 CDR Processing & Geolocation App

A fully Dockerized Symfony application for processing Call Detail Records (CDRs), geolocating origins and destinations, storing results, and dynamically displaying aggregated statistics.

---

## 🧠 Tech Stack

| Layer         | Tech                             |
|---------------|----------------------------------|
| Language      | PHP 8.1                          |
| Framework     | Symfony 6                        |
| DB            | MySQL 8 (Dockerized)             |
| ORM           | Doctrine                         |
| Frontend      | Twig + vanilla JavaScript (AJAX) |
| API Used      | ipgeolocation.io (IP → Continent)|
| Geo Data      | GeoNames countryInfo.txt         |
| Containerized | Docker + Docker Compose          |

---

## 📦 Features

- Upload CSV files containing CDRs
- Parse and store records in a relational database
- Resolve continent information based on:
  - IP address (via `ipgeolocation.io`)
  - Phone prefix (via GeoNames country data)
- Aggregate per-customer statistics:
  - Total calls
  - Total duration
  - Calls within the same continent
- Auto-refreshing frontend to display real-time data

---

## 🧱 Project Structure

```
cdr-report/
├── src/
│   ├── Controller/
│   ├── Entity/
│   ├── Form/
│   └── Service/
├── templates/
├── public/
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 🚀 How to Run

> Requires only Docker and Docker Compose installed locally.

```bash
docker-compose up --build
docker exec -it cdr_php php bin/console doctrine:migrations:migrate
```
⚠️ Note: The database is empty after the first launch. Running migrations will create the necessary table(s). Until then, stats and uploads will throw errors.

Then visit:

- Backend App: [http://localhost:8000/upload](http://localhost:8000/upload)
- phpMyAdmin: [http://localhost:8080](http://localhost:8080)  
  (Login: `root` / Password: `root`)

---

## 📝 Usage

1. Go to `/upload`
2. Upload a CSV file with the following format:

   ```
   Customer ID, Call Date, Duration, Phone Number, IP Address
   ```

3. Backend will:
   - Fetch continent data
   - Store all processed call records
   - Compute per-customer stats

4. The frontend will auto-poll and update stats table.

---

## 🧠 Notes

- IP continent resolution uses [ipgeolocation.io](https://ipgeolocation.io/documentation.html)
- Phone number → continent is parsed from [GeoNames countryInfo.txt](http://download.geonames.org/export/dump/countryInfo.txt)
- Post-Redirect-Get pattern is implemented to avoid resubmissions
- AJAX polling updates stats in real time

---

## 🐘 Built With

- ❤️ Symfony
- 🐳 Docker
- 📊 Doctrine ORM
- 🧠 Clean service-oriented PHP