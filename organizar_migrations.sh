#!/bin/bash
# Script para reorganizar migrations Laravel baseado nas dependências

MIGRATIONS_DIR="database/migrations"

# Lista na ordem correta
FILES=(
    "create_users_table.php"
    "create_password_reset_tokens_table.php"
    "create_failed_jobs_table.php"
    "create_personal_access_tokens_table.php"
    "create_permission_tables.php"
    "create_patients_table.php"
    "create_consultations_table.php"
    "create_exams_table.php"
    "create_exam_attachments_table.php"
    "create_vaccines_table.php"
    "create_home_visits_table.php"
)

# Data inicial para gerar timestamps
DATE_PREFIX=$(date +"%Y_%m_%d")
COUNTER=0

for FILE in "${FILES[@]}"; do
    COUNTER=$((COUNTER+1))
    TIMESTAMP=$(printf "%06d" $COUNTER)
    OLD_FILE=$(find "$MIGRATIONS_DIR" -type f -name "*$FILE" | head -n 1)

    if [[ -f "$OLD_FILE" ]]; then
        NEW_FILE="$MIGRATIONS_DIR/${DATE_PREFIX}_${TIMESTAMP}_$FILE"
        mv "$OLD_FILE" "$NEW_FILE"
        echo "✅ Renomeado: $OLD_FILE → $NEW_FILE"
    else
        echo "⚠️ Arquivo não encontrado: $FILE"
    fi
done

echo "🎯 Reorganização concluída!"
