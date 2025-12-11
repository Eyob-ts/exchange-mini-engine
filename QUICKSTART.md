# Quick Start Guide 🚀

## For Recruiters / Testers

### Prerequisites
1. **Docker Desktop** must be installed and running
   - Download: https://www.docker.com/products/docker-desktop
   - Make sure Docker Desktop is running before proceeding

### One-Command Setup

**On Windows:**
```bash
run.bat
```

**On Linux/Mac:**
```bash
chmod +x run.sh
./run.sh
```

### What Happens?
1. ✅ Creates `.env` files automatically
2. ✅ Builds Docker containers
3. ✅ Sets up MySQL database
4. ✅ Runs migrations
5. ✅ Starts all services

### Access the Application

After setup completes:
- **Frontend (Vue.js)**: http://localhost:5173
- **Backend API**: http://localhost:8000/api

### Test the Application

1. Open http://localhost:5173
2. Click "Register" to create an account
3. Login with your credentials
4. Try placing buy/sell orders!

### Troubleshooting

**Port already in use?**
- Stop other services using ports 8000 or 5173
- Or edit `docker-compose.yml` to use different ports

**Docker not running?**
- Make sure Docker Desktop is started
- Check with: `docker ps`

**Need to reset everything?**
```bash
docker-compose down -v
./run.sh  # or run.bat on Windows
```

### View Logs
```bash
docker-compose logs -f
```

### Stop Everything
```bash
docker-compose down
```

---

**Note**: First run may take 2-3 minutes to download images and install dependencies.

