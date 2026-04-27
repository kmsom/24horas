# Usa uma imagem oficial do PHP com servidor embutido
FROM php:8.2-cli

# Instala extensões necessárias para o seu bot (curl, ssl)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && docker-php-ext-install curl

# Define a pasta de trabalho dentro do container
WORKDIR /app

# Copia o seu código do bot para dentro do container
COPY . .

# Expõe a porta que o Render usa
EXPOSE 10000

# Comando para iniciar o servidor PHP na porta correta
CMD ["php", "-S", "0.0.0.0:10000", "index.php"]
