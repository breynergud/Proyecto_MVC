# Sistema de Asignaciones - Calendario

## Funcionalidad

El sistema de asignaciones permite gestionar la asignación de instructores a fichas mediante un calendario interactivo.

### Flujo de Trabajo

1. **Buscar Ficha**: 
   - Ingresa el número de ficha en el buscador superior izquierdo
   - Click en "Buscar" o presiona Enter
   - Se muestra la información de la ficha (programa, jornada, instructor líder)

2. **Visualizar Calendario**:
   - El calendario muestra el mes actual
   - Las asignaciones existentes aparecen como bloques verdes con el nombre del instructor
   - Los espacios en blanco indican días disponibles para nuevas asignaciones

3. **Crear Asignación**:
   - Click en cualquier día del calendario
   - Se abre un modal con:
     - Fecha seleccionada
     - Dropdown de competencias pendientes (solo las que faltan por asignar)
     - Dropdown de ambientes disponibles
     - Campos de hora inicio y fin
   
4. **Seleccionar Competencia**:
   - Al seleccionar una competencia, aparece automáticamente la lista de instructores especializados
   - Los instructores se muestran como tarjetas con:
     - Nombre completo
     - Correo electrónico
     - Centro de formación

5. **Seleccionar Instructor**:
   - Click en la tarjeta del instructor deseado
   - La tarjeta se marca como seleccionada (fondo verde claro)
   - Se habilita el botón "Guardar Asignación"

6. **Guardar**:
   - Click en "Guardar Asignación"
   - El sistema crea la asignación en la base de datos
   - El calendario se actualiza automáticamente mostrando la nueva asignación

### Características

- **Competencias Inteligentes**: Solo muestra competencias que:
  - Pertenecen al programa de la ficha
  - No han sido asignadas previamente

- **Navegación de Meses**: Botones para avanzar/retroceder meses

- **Validaciones**:
  - Todos los campos son obligatorios
  - No se puede guardar sin seleccionar instructor
  - Validación de horarios

- **Notificaciones Toast**: Mensajes de éxito/error en tiempo real

### Estructura de Base de Datos

```sql
asignacion:
- asig_id (PK)
- instructor_inst_id (FK)
- asig_fecha_ini (TIMESTAMP)
- asig_fecha_fin (TIMESTAMP)
- ficha_fich_id (FK)
- ambiente_amb_id (FK)
- competencia_comp_id (FK)
```

### Endpoints API

- `GET /asignacion/getFichaInfo?ficha_id=X` - Info de ficha
- `GET /asignacion/getAsignacionesByFicha?ficha_id=X` - Asignaciones de una ficha
- `GET /asignacion/getCompetenciasPendientes?ficha_id=X` - Competencias sin asignar
- `GET /asignacion/getInstructoresByCompetencia?competencia_id=X` - Instructores especializados
- `POST /asignacion/store` - Crear asignación

### Mejoras Futuras

- Tabla de especialidades instructor-competencia
- Vista de conflictos de horarios
- Exportar calendario a PDF
- Filtros por instructor/ambiente
- Vista semanal/diaria
- Drag & drop para mover asignaciones
