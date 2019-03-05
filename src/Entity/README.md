Generate the classes with for instance:
```bash
alias=Coa && \
rm -rf src/Entity/$alias/* && \
./bin/console doctrine:mapping:import 'App\Entity\'$alias annotation --path=src/Entity/$alias --em ${alias,,} && \
./bin/console make:entity --regenerate App
```