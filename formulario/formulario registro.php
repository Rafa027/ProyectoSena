
<html>
<head>
    <meta charset="UTF-8">
    <title>Regristo de usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <H1>REGISTRE SUS DATOS</H1>
    <form action="../PHP/registro.php" method="post">
        <div class="encabezado">
        <img src="logo-prueba.png" alt="logo" width="100px"><br><br>
        <a href="../index.html" class="atras">Volver atras</a></div>
        <label for="username">NOMBRES</label>
        <input type="text" id="Nombres" placeholder="Nombres completos" name="nombre" required>
        <label for="username">APELLIDOS</label>
        <input type="text" id="Apellidos" placeholder="Apellidos" name="apellido" required>
        <label for="username">TIPO DE DOCUMENTO</label>
        <select name="Tdocumento" id="" class="TD" required>
            <option value="">Seleccione su documento</option>
            <option value="Cedula de ciudadania">Cedula de ciudadania</option>
            <option value="Tarjeta de Identidad">Tarjeta de identidad</option>
            <option value="Cedula extranjera">Cedula Extranjera</option>
        </select>
        <label for="username">DOCUMENTO</label>
        <input type="text" id="documento" placeholder="Numero de documento" class="container" name="Ndocumento" required>
        <label for="username">CORREO ELECTRONICO</label>
        <input type="email" name="correo" id="" required>
        <label for="username">TELEFONO</label>
        <input type="number" id="Telefono" placeholder="# celular" class="numero" name="contacto" required>
        <label for="password">CONTRASEÑA</label>
        <input type="password" id="Contraseña" placeholder="contraseña" class="password" name="password" required><br><br>
         <!-- Mostrar el checkbox solo si el usuario es administrador -->
        <button type="submit">Crear usuario</button><br><br>
        <a href="../programacion/sign-in.php" class="login">Ya tengo cuenta</a>
    </form>
    <script src="index.js"></script>
</body>

</html>