# Upgrade from 1.x to 2.0

## Sonata Admin

 * The SonataAdminBundle integration has been moved to
   `symfony-cmf/sonata-admin-integration-bundle`. This includes the classes in
   the `Admin` namespace and related services; and the `use_sonata_admin`,
   `*_document_class` and `*_admin_class` settings.

   **Before**
   ```yaml
   # app/config/config.yml
   sonata_admin:
       extensions:
           cmf_block.admin_extension.cache:
               # ...
   ```

   **After**
   ```yaml
   # app/config/config.yml
   sonata_admin:
       extensions:
           cmf_sonata_admin_integration.block.extension.cache:
               # ...
   ```
