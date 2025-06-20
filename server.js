const express = require('express');
const path = require('path');
const fs = require('fs');
const axios = require('axios');
const app = express();

app.use(express.static(path.join(__dirname, 'public')));

app.get('/api/ip', async (req, res) => {
  const rawIp = req.headers['x-forwarded-for'] || req.socket.remoteAddress;
  const ip = rawIp.split(',')[0].trim().replace(/^::ffff:/, '');

  try {
    // Obtener detalles de la IP desde ipapi.co
    const response = await axios.get(`https://ipapi.co/${ip}/json/`);
    const data = response.data;

    const userAgent = req.headers['user-agent'];

    const log = `
==== NUEVA VISITA ====
Fecha: ${new Date().toISOString()}
IP: ${data.ip}
Ciudad: ${data.city}
Región: ${data.region}
País: ${data.country_name}
Código país: ${data.country}
Latitud: ${data.latitude}
Longitud: ${data.longitude}
Proveedor (ISP): ${data.org}
Tipo de red: ${data.network}
Navegador/SO: ${userAgent}
======================
`;

    // Guardar en archivo
    fs.appendFile('ips.txt', log, err => {
      if (err) console.error('Error al guardar datos:', err);
    });

    res.json({ ip: data.ip, ciudad: data.city, pais: data.country_name });
  } catch (error) {
    console.error('Error al obtener info de IP:', error.message);
    res.json({ ip });
  }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor ejecutándose en https://angelpp27.github.io/Angelpp27/:${PORT}`);
});
