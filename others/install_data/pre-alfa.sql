SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";



SET time_zone = "+00:00";



CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(64) NOT NULL,
  `foto_css` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `foto_css`) VALUES
(1, 'Animaciones', 'flash'),
(2, 'Arte', 'arte'),
(3, 'Autos y motos', 'autosymotos'),
(4, 'Comics', 'comics'),
(5, 'Celulares', 'celulares'),
(6, 'Ciencia y educación', 'cienciyeducacion'),
(7, 'Deportes', 'deportes'),
(8, 'Descargas', 'descargas'),
(9, 'Ecología', 'ecología'),
(10, 'Links', 'links'),
(11, 'Femenino', 'femme'),
(12, 'Fotos e imagenes', 'imagenes'),
(13, 'Hazlo tú mismo', 'doityourself'),
(14, 'Homosexual', 'gay'),
(15, 'Humor', 'humor'),
(16, 'Información', 'info'),
(17, 'Juegos', 'juegos'),
(18, 'Juegos online', 'juegosonline'),
(19, 'Linux', 'linux'),
(20, 'Música', 'musica'),
(21, 'Mac', 'mac'),
(22, 'Manga y anime', 'mangayanime'),
(23, 'Mascotas', 'mascotas'),
(24, 'Monografías', 'monografias'),
(25, 'Negocios y economía', 'negociosyeconomia'),
(26, 'Noticias', 'noticias'),
(27, 'Offtopic', 'offtopic'),
(28, 'Paranormal', 'paranormal'),
(29, 'Recetas y cocina', 'recetas'),
(30, 'Reviews', 'reviews'),
(31, 'Salud y bienestar', 'saludybienestar'),
(32, 'Soliradidad', 'solidaridad'),
(33, 'Turismo', 'turismo'),
(34, 'Tutoriales', 'tutoriales'),
(35, 'TV, Peliculas, Series', 'tvpeliculasyseries'),
(36, 'Vídeos online', 'videos');



CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_moderador` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `razon_editado` varchar(250) NOT NULL,
  `positivos` int(11) NOT NULL,
  `negativos` int(11) NOT NULL,
  `fecha` int(11) NOT NULL,
  `id_post` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `favoritos_post` (
  `id_usuario` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `paises` (
  `id_pais` int(11) NOT NULL,
  `nombre_pais` varchar(32) NOT NULL,
  `foto_css` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `paises` (`id_pais`, `nombre_pais`, `foto_css`) VALUES
(1, 'Argentina', '');



CREATE TABLE `post` (
  `id_post` bigint(20) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `contenido` text NOT NULL,
  `categoria` int(11) NOT NULL,
  `comentarios` int(11) NOT NULL,
  `publico` int(11) NOT NULL,
  `puntos` int(11) NOT NULL,
  `visitas` int(11) NOT NULL,
  `favoritos` int(11) NOT NULL,
  `seguidores` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `puntos_comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `puntos_post` (
  `id_usuario` int(11) NOT NULL,
  `id_post` bigint(20) NOT NULL,
  `fecha` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `rangos` (
  `id_rango` int(11) NOT NULL,
  `nombre_rango` varchar(32) NOT NULL,
  `puntos_disponibles` int(11) NOT NULL,
  `foto_css` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `rangos` (`id_rango`, `nombre_rango`, `puntos_disponibles`, `foto_css`) VALUES
(1, 'Novato', 0, 'new'),
(2, 'Administrador', 100, 'admin'),
(3, 'Moderador', 35, 'moderator'),
(4, 'New full user', 10, 'nfu'),
(5, 'Full user', 15, 'medalstar green'),
(6, 'Great user', 20, 'medalstar red');



CREATE TABLE `seguidores_post` (
  `id_post` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `seguidores_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_seguidor` int(11) NOT NULL,
  `fecha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `sesiones` (
  `id_usuario` int(11) NOT NULL,
  `last_activity` int(11) NOT NULL,
  `php_sessid` varchar(250) NOT NULL,
  `ubicacion` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `tags` (
  `id_tag` int(11) NOT NULL,
  `texto_tag` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `tags_post` (
  `id_post` bigint(20) NOT NULL,
  `id_tag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nick` varchar(32) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `id_rango` int(11) NOT NULL,
  `sexo` int(11) NOT NULL,
  `dia_n` int(11) NOT NULL,
  `mes_n` int(11) NOT NULL,
  `anio_n` int(11) NOT NULL,
  `pais` int(11) NOT NULL,
  `region` varchar(64) NOT NULL,
  `status_user` int(11) NOT NULL,
  `token_activacion` varchar(250) NOT NULL,
  `puntos_obtenidos` int(11) NOT NULL,
  `puntos_disponibles` int(11) NOT NULL,
  `post_creados` int(11) NOT NULL,
  `comentarios_creados` int(11) NOT NULL,
  `seguidores` int(11) NOT NULL,
  `siguiendo` int(11) NOT NULL,
  `mensaje_perfil` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);


  
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`,`id_usuario`,`id_moderador`,`id_post`);


  
ALTER TABLE `favoritos_post`
  ADD PRIMARY KEY (`id_usuario`,`id_post`);


  
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id_pais`);


  
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`,`id_usuario`);


  
ALTER TABLE `puntos_comentarios`
  ADD PRIMARY KEY (`id_comentario`,`id_usuario`);


  
ALTER TABLE `puntos_post`
  ADD PRIMARY KEY (`id_usuario`,`id_post`);


  
ALTER TABLE `rangos`
  ADD PRIMARY KEY (`id_rango`);


  
ALTER TABLE `seguidores_post`
  ADD PRIMARY KEY (`id_post`,`id_usuario`);


  
ALTER TABLE `seguidores_usuarios`
  ADD PRIMARY KEY (`id_usuario`,`id_seguidor`);


  
ALTER TABLE `sesiones`
  ADD PRIMARY KEY (`id_usuario`);


  
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id_tag`,`texto_tag`);


  
ALTER TABLE `tags_post`
  ADD PRIMARY KEY (`id_post`,`id_tag`);


  
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`,`nick`);


  
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;


  
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;


  
ALTER TABLE `paises`
  MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


  
ALTER TABLE `post`
  MODIFY `id_post` bigint(20) NOT NULL AUTO_INCREMENT;


ALTER TABLE `rangos`
  MODIFY `id_rango` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `tags`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;


