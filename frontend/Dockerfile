# Multi-stage build untuk frontend Vue.js (Soal 5b)

# Tahap 1: Build aplikasi dengan Node.js
FROM node:20.18-alpine AS builder

WORKDIR /app

# Salin package.json & package-lock.json terlebih dahulu untuk layer cache
COPY package*.json ./

# Pasang dependensi secara bersih
RUN npm ci

# Salin kode sumber frontend
COPY . .

# Build aplikasi Vue (menghasilkan berkas statis di /app/dist)
RUN npm run build

# Tahap 2: Sajikan hasil build dengan Nginx (image akhir tanpa node_modules)
FROM nginx:1.27-alpine AS production

# Salin konfigurasi kustom Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Salin artefak statis dari builder stage
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
