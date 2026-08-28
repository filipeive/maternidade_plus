# UNIVERSIDADE LICUNGO
## FACULDADE DE CIÊNCIAS DE SAÚDE / FACULDADE DE CIÊNCIAS E TECNOLOGIA
### DEPARTAMENTO DE SAÚDE PÚBLICA E INFORMÁTICA MÉDICA
**CAMPUS UNIVERSITÁRIO DE QUELIMANE — ZAMBÉZIA**

---

# PROJECTO DE INVESTIGAÇÃO CIENTÍFICA

### **TÍTULO:**
> **MATERNIDADE+: IMPLEMENTAÇÃO DE UM SISTEMA DIGITAL DE ALERTA PRECOCE, ENVOLVIMENTO FAMILIAR E MONITORIA DA ATENÇÃO PRÉ-NATAL (MISAU) PARA A REDUÇÃO DA MORTALIDADE MATERNO-INFANTIL NO MUNICÍPIO DE QUELIMANE**

**Linha de Pesquisa:** Saúde Pública, Epidemiologia e Tecnologias de Informação em Saúde (e-Health/m-Health)  
**Evento Alvo:** Jornadas Científicas da Universidade Licungo  
**Proponente / Investigador Principal:** Docente Universitário & Equipa de Investigação Maternidade+  
**Local:** Quelimane, Província da Zambézia, Moçambique  
**Ano Académico:** 2026  

---

## RESUMO (ABSTRACT)

### Resumo em Português
A mortalidade materna e neonatal continua a representar um dos mais severos desafios de saúde pública em Moçambique, com o Município de Quelimane a registar perdas substanciais decorrentes de atrasos no reconhecimento do Alto Risco Obstétrico (ARO), descontinuidades nas consultas de Acompanhamento Pré-Natal (CPN) e fraca participação da rede de suporte familiar. Este projecto de investigação científica propõe a implementação, validação clínica e avaliação do impacto do ecossistema digital **Maternidade+** em Unidades Sanitárias estratégicas de Quelimane (Centro de Saúde de Quelimane Urbano, Centro de Saúde de Coalane e Hospital Geral de Quelimane). O sistema digitaliza integralmente a Ficha Pré-Natal (FPN), o Livro Eletrónico MOD-SIS-B01 e a estratificação de ARO segundo as Normas Nacionais do MISAU (Níveis I, II e III), integrando algoritmos preditivos de alerta precoce e envio automatizado de SMS para busca ativa de faltosas direcionado tanto à gestante quanto ao seu parceiro ou acompanhante familiar de apoio (mães, tias, irmãs) em casos de vulnerabilidade ou ausência de telefone próprio. A metodologia adopta uma abordagem mista (quanti-qualitativa), de intervenção quase-experimental longitudinal antes-e-depois ($n = 500$). Os resultados esperados incluem a redução de $\ge 40\%$ no abandono de consultas CPN, aumento de $\ge 50\%$ na adesão à coorte de $\ge 4$ consultas, triagem atempada em 100% dos casos de pré-eclâmpsia e isoimunização Rh, e eliminação de atrasos na consolidação dos resumos estatísticos municipais (MOD-SIS-B01-C).

**Palavras-chave:** Saúde Materno-Infantil, Atenção Pré-Natal, Alerta Precoce, Apoio Familiar, e-Health, Normas MISAU, Município de Quelimane, Universidade Licungo.

### Abstract in English
Maternal and neonatal morbidity and mortality remain persistent public health concerns in Mozambique. In Quelimane Municipality, preventable maternal deaths frequently stem from delayed recognition of High Obstetric Risk (ARO), high antenatal care (ANC) dropout rates, and insufficient involvement of family support networks. This research project investigates the clinical effectiveness, technological acceptance, and community impact of the **Maternidade+** digital health platform across key health facilities in Quelimane (Quelimane Urban Health Center, Coalane Health Center, and Quelimane General Hospital). The platform digitizes the National Antenatal Card (FPN), the Electronic Register MOD-SIS-B01, and MISAU Risk Stratification Protocols (Levels I, II, and III), featuring predictive early warning alerts and automated bidirectional SMS recall sent to both pregnant women and designated family companions (mothers, aunts, sisters, partners)—crucial for women facing abandonment, teenage pregnancy, or lack of personal mobile devices. Employing a mixed-methods quasi-experimental longitudinal design ($n = 500$), expected outcomes include a $\ge 40\%$ drop in ANC appointment defaults, a $\ge 50\%$ increase in $\ge 4$ ANC cohort compliance, 100% timely referral of severe preeclampsia and Rh isoimmunization, and real-time municipal health reporting (MOD-SIS-B01-C).

**Keywords:** Maternal and Child Health, Antenatal Care, Early Warning Systems, Family Support Network, mHealth, MISAU Protocols, Quelimane Municipality, Licungo University.

---

## 1. INTRODUÇÃO & CONTEXTUALIZAÇÃO

A redução da mortalidade materna e neonatal insere-se no núcleo dos Objectivos de Desenvolvimento Sustentável (ODS 3, Metas 3.1 e 3.2) e das prioridades estratégicas do Governo de Moçambique expressas no Plano Estratégico do Sector da Saúde (PESS). No contexto local da Província da Zambézia, o **Município de Quelimane** concentra uma densidade populacional urbana e periurbana elevada, onde convergem utentes locais e casos referenciados de distritos circunvizinhos, sobrecarregando a rede de atenção primária e hospitalar.

O Ministério da Saúde (MISAU) define directrizes rigorosas para o seguimento pré-natal, preconizando pelo menos 4 consultas bem estruturadas (idealmente 8 contactos), vigilância da pressão arterial, profilaxia da malária (TIP-SP), vacinação antitetânica (VAT), rastreio de anemia, HIV, Sífilis e incompatibilidade sanguínea de factor Rh. Entretanto, no terreno, o registo manual em suporte de papel enfrenta três entraves estruturais:
1. **Perda de Rastreabilidade e Faltas Não Notadas:** Gestantes que faltam a consultas consecutivas não são detectadas em tempo útil pela equipa de Enfermagem de Saúde Materno-Infantil (ESMI).
2. **Vulnerabilidade Social e Falta de Envolvimento Familiar:** Muitas gestantes adolescentes ou sem companheiro fixo enfrentam isolamento; sem o envolvimento ativo de um familiar responsável (mãe, tia, irmã ou sogra) para partilhar lembretes de consulta, o risco de abandono aumenta exponencialmente.
3. **Sobrecarga Burocrática no Fecho Estatístico:** A compilação mensal dos livros MOD-SIS-B01 consome preciosas horas de trabalho clínico das enfermeiras.

Perante esta realidade, a **Universidade Licungo (UniLicungo)**, enquanto pólo de excelência universitária e de investigação sediado em Quelimane, propõe o projecto de validação científica e clínica da plataforma digital **Maternidade+**.

---

## 2. PROBLEMATIZAÇÃO & PERGUNTAS DE INVESTIGAÇÃO

### 2.1. Problematização
No Município de Quelimane:
- As taxas de captação precoce da gravidez ($\le 12$ semanas) e a retenção até ao final da gestação situam-se frequentemente abaixo das metas nacionais do MISAU.
- Uma parcela considerável de gestantes não possui telemóvel próprio ou é abandonada pelos parceiros durante a gestação, tornando ineficazes os sistemas de contacto que dependem exclusivamente de um único número da utente.
- A identificação de sinais clínicos de alarme (hipertensão $\ge 140/90\text{ mmHg}$, proteinúria, anemia grave $\text{Hb} \le 7\text{ g/dL}$, incompatibilidade Rh) ocorre muitas vezes tarde demais para intervenção preventiva nos Centros de Saúde.

### 2.2. Questões de Pesquisa
- **Questão Principal:** Qual é o impacto da implementação do sistema digital Maternidade+ — que combina alertas precoces do MISAU e notificações SMS dirigidas tanto à gestante quanto à sua rede de apoio familiar — na adesão ao pré-natal e na redução de complicações materno-infantis no Município de Quelimane?
- **Questões Específicas:**
  1. Em que medida a inclusão do contacto do acompanhante/familiar de apoio (mãe, tia, parceiro) aumenta a taxa de retorno de gestantes faltosas em comparação com o modelo convencional?
  2. Como a triagem digital automatizada de Alto Risco Obstétrico (ARO Níveis I, II e III) influencia o tempo de resposta e referência hospitalar para o Hospital Geral/Central de Quelimane?
  3. Qual é o ganho de eficiência no preenchimento e consolidação dos relatórios estatísticos do SIS (MOD-SIS-B01-B e C)?

---

## 3. HIPÓTESES DE INVESTIGAÇÃO

- **Hipótese Nula ($H_0$):** A utilização da plataforma Maternidade+ e o envio de alertas SMS à rede de apoio familiar não provocam alteração estatisticamente significativa na retenção de gestantes nem na identificação precoce de ARO no Município de Quelimane.
- **Hipótese Alternativa ($H_1$):** A implementação do Maternidade+ eleva a retenção na coorte de $\ge 4$ consultas CPN em mais de 50%, reduz o tempo de referência de casos de emergência obstétrica e assegura 100% de fiabilidade nos relatórios estatísticos distritais.

---

## 4. OBJECTIVOS DO ESTUDO

### 4.1. Objectivo Geral
Implementar e avaliar a eficácia clínica, a adesão comunitária e o impacto operacional da plataforma digital **Maternidade+** no acompanhamento pré-natal, na triagem de risco obstétrico (MISAU) e no fortalecimento da rede de suporte familiar no Município de Quelimane.

### 4.2. Objectivos Específicos
1. Desenvolver e parametrizar o módulo de Rede de Apoio Familiar com envio de SMS de lembretes e alertas para gestantes, parceiros e acompanhantes responsáveis (mães/tias).
2. Avaliar a taxa de captação precoce ($\le 12$ semanas) e retenção ($\ge 4$ consultas CPN) antes e após a entrada em funcionamento do sistema no Centro de Saúde de Quelimane Urbano e CS de Coalane.
3. Testar a sensibilidade e especificidade dos algoritmos de Alerta Precoce para pré-eclâmpsia, anemia severa e incompatibilidade de factor Rh.
4. Mensurar a redução do tempo de geração e a consistência dos relatórios mensais MOD-SIS-B01-B (Unidade Sanitária) e MOD-SIS-B01-C (Distrito de Quelimane).
5. Avaliar a usabilidade do sistema pelos profissionais de saúde através da escala padronizada SUS (*System Usability Scale*).

---

## 5. REVISÃO DA LITERATURA & QUADRO TEÓRICO

```
                             ARQUITETURA DE IMPACTO COMUNITÁRIO — QUELIMANE
+-------------------------------------------------------------------------------------------------------+
|                                    GESTANTE NO MUNICÍPIO DE QUELIMANE                                 |
+---------------------------------------------------+---------------------------------------------------+
                                                    |
                         +--------------------------+--------------------------+
                         |                                                     |
                         v                                                     v
            [COM PARCEIRO PRESENTE]                                [SEM PARCEIRO / VULNERÁVEL]
         (Registo de Nome, Tel & Sangue)                       (Acompanhante: Mãe, Tia, Irmã, Vizinha)
                         |                                                     |
                         +--------------------------+--------------------------+
                                                    |
                                                    v
+-------------------------------------------------------------------------------------------------------+
|                                     PLATAFORMA DIGITAL MATERNIDADE+                                   |
|   - Ficha Pré-Natal Digital (FPN)                  - Triagem ARO Automática (Níveis I, II e III)      |
|   - Rastreio Rh (Mãe Rh- / Parceiro Rh+)           - Alertas Imediatos (PA >= 140/90, Hb <= 7 g/dL)   |
|   - Gestão de Coortes CPN & Parto (APGAR/Vit K)    - Disparo Inteligente de SMS para Gestante & Apoio |
+---------------------------------------------------+---------------------------------------------------+
                                                    |
                                                    v
+-------------------------------------------------------------------------------------------------------+
|                                           DESFECHO CLÍNICO                                            |
|        - Retenção CPN >= 85%                           - Referência Hospitalar Segura (HGQ / HCQ)     |
|        - Zero Perdas por Desconhecimento de Consulta   - Relatórios Distritais B01-C em Tempo Real    |
+-------------------------------------------------------------------------------------------------------+
```

### 5.1. A Importância da Rede de Apoio Familiar no Contexto Moçambicano
Na sociedade moçambicana, a gravidez e o parto são vivenciados como eventos familiares e comunitários. Em muitas comunidades periurbanas de Quelimane (ex: Coalane, Sangalieve, Icidua, Chuabo Dembe), a figura materna, as tias ou as irmãs mais velhas desempenham o papel de conselheiras e garantem o transporte e o acompanhamento da gestante à Unidade Sanitária. Quando o sistema de saúde inclui ativamente esse acompanhante no canal de comunicação, o índice de faltas reduz drasticamente.

### 5.2. O Modelo Clínico MISAU e a Triagem ARO em Quelimane
As Normas Nacionais de CPN (MISAU, 2018; 2021) estipulam fluxos claros:
- Gestantes sem fatores de risco são acompanhadas nas consultas de rotina do Centro de Saúde.
- Gestantes com patologias moderadas (Nível I e II) são avaliadas por Médicos/Técnicos de Medicina ou agendadas para o Hospital Rural/Geral às 32 semanas.
- Gestantes de Nível III (adolescentes $<16$ anos, baixa estatura $<1,50$ m, cesariana anterior, fórceps prévio) devem ter o parto planeado no Hospital Geral ou Central de Quelimane, munidas do checklist de emergência (veia canalizada, algaliação, acompanhante para doação de sangue e FPN completa).

---

## 6. METODOLOGIA DE INVESTIGAÇÃO

O estudo segue as diretrizes do **Manual de Normas de Elaboração de Trabalhos Científicos da Universidade Licungo (Edição 2024)** e formatação **APA 7ª Edição**.

### 6.1. Tipo de Pesquisa & Delineamento
- **Natureza:** Aplicada, tecnológica e de intervenção em saúde pública.
- **Abordagem:** Mista (Quali-Quantitativa).
- **Delineamento:** Quase-experimental, prospectivo longitudinal de coorte, com comparação dos indicadores históricos (período pré-intervenção) versus período sob uso do Maternidade+.

### 6.2. Campo de Investigação (Delimitação Geográfica)
O estudo será circumscrito ao **Município / Distrito de Quelimane**, abrangendo:
1. **Centro de Saúde de Quelimane Urbano** (Unidade piloto de atenção primária).
2. **Centro de Saúde de Coalane** (Unidade periurbana de alta densidade).
3. **Hospital Geral de Quelimane (HGQ)** (Nível de referência para casos ARO).

*Justificativa da delimitação:* A escolha do Município de Quelimane permite um controlo metodológico rigoroso, acompanhamento direto pelos docentes e estudantes da Faculdade de Ciências de Saúde da UniLicungo, proximidade logística e rápida validação empírica antes da extensão a toda a Província da Zambézia.

### 6.3. População e Amostragem
- **População:** Mulheres grávidas residentes no Município de Quelimane que iniciam o pré-natal durante o período de estudo.
- **Amostra:** $n = 500$ gestantes distribuídas pelas unidades participantes.
- **Profissionais de Saúde:** $n = 25$ enfermeiras de SMI, médicos e técnicos envolvidos no atendimento pré-natal.

### 6.4. Instrumentos de Recolha e Variáveis de Estudo
- **Variáveis Independentes:** Uso do sistema Maternidade+, inclusão da rede de apoio familiar, disparo de SMS de lembrete e estratificação automática de ARO.
- **Variáveis Dependentes:** Idade gestacional na 1ª consulta, número total de consultas CPN realizadas, taxa de adesão às profilaxias (TIP/SP, VAT, Ferro/Folato), identificação de complicações (hipertensão, anemia, sífilis, incompatibilidade Rh), desfecho do parto (APGAR, peso ao nascer, mortalidade perinatal) e tempo de processamento dos relatórios MOD-SIS.
- **Instrumentos de Usabilidade:** Escala SUS (*System Usability Scale*) e questionários estruturados de aceitação tecnológica.

### 6.5. Análise Estatística
- Análise univariada e bivariada através do **SPSS v28** e **R**.
- Testes Qui-Quadrado ($\chi^2$) para variáveis categóricas, Teste t de Student para variáveis contínuas emparelhadas e Regressão de Cox/Logística para análise da retenção na coorte. Nível de significância: $p < 0,05$ com Intervalo de Confiança de 95%.

### 6.6. Aspectos Éticos e Bioéticos
- Submissão ao **Comité Institucional de Bioética para a Saúde da Universidade Licungo (CIBS-UniLicungo)** e aprovação pela Direcção Provincial de Saúde da Zambézia.
- Obtenção do Termo de Consentimento Livre e Esclarecido (TCLE) com garantia absoluta de confidencialidade, anonimização de dados pessoais e respeito integral pelos princípios da Declaração de Helsínquia.

---

## 7. RESULTADOS ESPERADOS & IMPACTO NA COMUNIDADE DE QUELIMANE

| Indicador | Situação Atual Estimada | Meta com Maternidade+ | Impacto Esperado em Quelimane |
| :--- | :---: | :---: | :--- |
| **Captação Precoce ($\le 12$ sem)** | $\approx 20\%$ | $\ge 45\%$ | Início atempado de suplementação e profilaxias. |
| **Retenção ($\ge 4$ Consultas CPN)** | $\approx 48\%$ | $\ge 80\%$ | Acompanhamento integral da gestação. |
| **Retorno de Faltosas via SMS Familiar** | $\approx 15\%$ | $\ge 65\%$ | Resgate de gestantes antes que surjam complicações. |
| **Deteção & Encaminhamento ARO** | Subnotificado | 100% dos critérios MISAU | Encaminhamento seguro ao HGQ antes do parto. |
| **Rastreio de Incompatibilidade Rh** | Esporádico | 100% de deteção precoce | Prevenção da eritroblastose fetal. |
| **Tempo de Fecho do MOD-SIS-B01-C** | 3 a 5 dias | Geração Instantânea | Informação em tempo real para a gestão municipal de saúde. |

---

## 8. CRONOGRAMA DE ATIVIDADES (12 MESES)

| Etapas do Projecto | M1 | M2 | M3 | M4 | M5 | M6 | M7 | M8 | M9 | M10 | M11 | M12 |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **1. Aprovação Ética (CIBS-UniLicungo)** | █ | █ | | | | | | | | | | |
| **2. Formação das Enfermeiras de SMI em Quelimane** | | █ | █ | | | | | | | | | |
| **3. Implementação Piloto (CS Urbano e Coalane)** | | | █ | █ | █ | █ | █ | █ | | | | |
| **4. Monitorização de Consultas & Alertas SMS** | | | | █ | █ | █ | █ | █ | █ | █ | | |
| **5. Coleta de Dados & Entrevistas SUS** | | | | | | | █ | █ | █ | █ | | |
| **6. Análise Estatística dos Desfechos Clínicos** | | | | | | | | | | █ | █ | |
| **7. Apresentação nas Jornadas Científicas da UniLicungo** | | | | | | | | | | | █ | █ |
| **8. Publicação de Artigo em Revista Internacional** | | | | | | | | | | | | █ |

---

## 9. ORÇAMENTO ESTIMATIVO

| Item / Rubrica | Justificação | Valor Estimado (MZN) |
| :--- | :--- | ---: |
| **1. Recursos Humanos** | Assistentes de Pesquisa de Campo da UniLicungo | 150.000,00 MT |
| **2. Infraestrutura Digital & Servidor** | Hospedagem em Nuvem Segura e Gateway SMS Local | 95.000,00 MT |
| **3. Oficinas de Capacitação** | Treinamento das Enfermeiras ESMI em Quelimane | 60.000,00 MT |
| **4. Material de Consumo** | Formulários de Pesquisa, Termos TCLE e Cartões QR | 30.000,00 MT |
| **5. Divulgação Científica** | Apresentação em Jornadas UniLicungo e Taxas de Artigo | 45.000,00 MT |
| **TOTAL GERAL ESTIMADO** | | **380.000,00 MT** |

---

## 10. REFERÊNCIAS BIBLIOGRÁFICAS (APA 7ª EDIÇÃO)

- Agarwal, S., Perry, H. B., Long, L. A., & Labrique, A. B. (2021). Evidence on features for effective mobile health solutions supporting community health workers. *BMJ Innovations*, 7(2), 347–356. https://doi.org/10.1136/bmjinnov-2020-000599
- Instituto Nacional de Estatística — INE. (2023). *Inquérito Demográfico e de Saúde de Moçambique (IDS 2022-2023)*. Maputo: INE & ICF.
- Ministério da Saúde — MISAU. (2018). *Manual de Normas da Atenção Pré-Natal e Cuidados Pós-Natais para Mulheres e Recém-Nascidos*. Direcção Nacional de Saúde Pública (DNSP), Maputo.
- Ministério da Saúde — MISAU. (2020). *Livro de Registos da Consulta Pré-Natal (MOD-SIS-B01) e Formulários Resumo (MOD-SIS-B01-B, C e D)*. Direcção de Planificação e Cooperação, Maputo.
- Ministério da Saúde — MISAU. (2021). *Manual Técnico do Pré-Natal e Puerpério / Ficha Pré-Natal (FPN)*. Programa Nacional de Saúde Materno-Infantil, Maputo.
- Organização Mundial da Saúde — OMS. (2016). *Recomendações da OMS sobre Cuidados Pré-Natais para uma Experiência Positiva na Gravidez*. Genebra: World Health Organization.
- Universidade Licungo — UniLicungo. (2024). *Normas de Elaboração e Publicação de Trabalhos Científicos na Universidade Licungo* (Edição 2024). Comissão Científica da UniLicungo, Quelimane.
- World Health Organization — WHO. (2019). *WHO guideline: Recommendations on digital interventions for health system strengthening*. Geneva: World Health Organization.

