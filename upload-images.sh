#!/bin/bash
set -e

# Configuration
VPS_IP="101.32.116.27"
VPS_USER="root"
VPS_PORT="22"

echo "=========================================================="
echo "🚀 JBB Website Image Uploader to Coolify"
echo "=========================================================="
echo "Script ini akan mengompresi gambar lokal Anda di:"
echo "👉 storage/app/public"
echo "dan mengunggahnya ke server VPS ($VPS_IP) secara aman."
echo "=========================================================="
echo ""

# Ask for confirmation
read -p "Apakah Anda ingin melanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "❌ Dibatalkan."
    exit 0
fi

# Step 1: Compress local storage images
echo ""
echo "📦 Step 1: Mengompresi folder storage/app/public lokal..."
if [ ! -d "storage/app/public" ]; then
    echo "❌ Error: Folder 'storage/app/public' tidak ditemukan!"
    exit 1
fi

tar -czf public-storage.tar.gz -C storage/app/public .
echo "✅ Berhasil dikompresi: public-storage.tar.gz"

# Step 2: Upload file to VPS
echo ""
echo "📤 Step 2: Mengunggah file ke VPS ($VPS_IP)..."
echo "Silakan masukkan password SSH VPS Anda jika diminta."
scp -P $VPS_PORT public-storage.tar.gz $VPS_USER@$VPS_IP:/tmp/public-storage.tar.gz

# Step 3: Extract inside the Docker Container on the VPS
echo ""
echo "🐳 Step 3: Menemukan container aplikasi JBB di VPS dan mengekstrak file..."
echo "Menghubungkan ke VPS via SSH..."

ssh -p $VPS_PORT $VPS_USER@$VPS_IP << 'EOF'
  set -e
  
  # Cari container aplikasi yang sedang berjalan
  # Kita cari container yang namanya mirip dengan 'jbb' atau 'javas' atau container web app
  CONTAINER_ID=$(docker ps --format "{{.Names}}" | grep -E "jbb|javas" | grep -v "database" | grep -v "redis" | head -n 1)
  
  if [ -z "$CONTAINER_ID" ]; then
    # Fallback: cari container non-database, non-redis, non-coolify yang running
    CONTAINER_ID=$(docker ps --format '{{.Names}}' | grep -vE "db|database|redis|coolify|postgres|mysql" | head -n 1)
  fi
  
  if [ -z "$CONTAINER_ID" ]; then
    echo "❌ Error: Tidak dapat menemukan container aplikasi JBB yang berjalan di VPS!"
    rm -f /tmp/public-storage.tar.gz
    exit 1
  fi
  
  echo "✅ Menemukan container aplikasi: $CONTAINER_ID"
  
  # Copy archive ke dalam container
  docker cp /tmp/public-storage.tar.gz $CONTAINER_ID:/tmp/public-storage.tar.gz
  
  # Ekstrak di dalam container ke path volume storage/app/public (menggunakan -u root agar tidak bermasalah dengan permission)
  echo "📂 Mengekstrak gambar ke folder storage persistent..."
  docker exec -u root $CONTAINER_ID bash -c "mkdir -p /app/storage/app/public && tar -xzf /tmp/public-storage.tar.gz -C /app/storage/app/public && chmod -R 777 /app/storage/app/public && chown -R 1000:1000 /app/storage/app/public || chmod -R 777 /app/storage/app/public"
  
  # Bersihkan temporary files di container dan VPS host
  docker exec -u root $CONTAINER_ID rm -f /tmp/public-storage.tar.gz
  rm -f /tmp/public-storage.tar.gz
  
  # Pastikan link storage diperbarui
  docker exec $CONTAINER_ID php artisan storage:link --force || true
  
  echo "✅ Sinkronisasi gambar ke volume hosting selesai dengan aman!"
EOF

# Step 4: Cleanup local temporary file
rm -f public-storage.tar.gz

echo ""
echo "=========================================================="
echo "🎉 PROSES SELESAI DENGAN SUKSES! 🎉"
echo "Semua gambar telah diunggah dan disimpan ke Volume persistent."
echo "Gambar Anda aman dari deploy ulang dan tidak akan terhapus!"
echo "=========================================================="
