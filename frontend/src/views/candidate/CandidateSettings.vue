<template>
  <div class="candidate-settings">
    <!-- Main Form Section -->
    <div class="settings-card">
      <h2 class="settings-title">Profile Settings</h2>

      <!-- Personal Information -->
      <div class="settings-section">
        <h3>Personal Information</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Full Name</label>
            <input
              v-model="formData.user.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': v$.user.name.$error }"
            />
            <div v-if="v$.user.name.$error" class="invalid-feedback">
              Name is required
            </div>
          </div>

          <div class="form-group">
            <label>Email Address</label>
            <input
              v-model="formData.user.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': v$.user.email.$error }"
            />
            <div v-if="v$.user.email.$error" class="invalid-feedback">
              Valid email is required
            </div>
          </div>
        </div>
      </div>

      <!-- Professional Information -->
      <div class="settings-section">
        <h3>Professional Information</h3>
        <div class="form-grid">
          <div class="form-group">
            <label>Professional Headline</label>
            <input
              v-model="formData.headline"
              type="text"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>LinkedIn Profile</label>
            <input
              v-model="formData.linkedin_url"
              type="url"
              class="form-control"
              placeholder="https://linkedin.com/in/your-profile"
            />
          </div>

          <div class="form-group">
            <label>Years of Experience</label>
            <select v-model="formData.experience_years" class="form-control">
              <option v-for="n in 30" :key="n" :value="n">{{ n }} years</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Skills Section -->
      <div class="settings-section">
        <div class="section-header">
          <h3>Skills & Expertise</h3>
          <button class="btn btn-primary" @click="showAddSkillForm = !showAddSkillForm">
            {{ showAddSkillForm ? 'Hide' : 'Add Skill' }}
          </button>
        </div>

        <!-- Inline Add Skill Form -->
        <div v-if="showAddSkillForm" class="add-skill-form">
          <div class="form-group">
            <label>Skill Name</label>
            <input v-model="newSkill.name" type="text" class="form-control" />
          </div>
          <div class="form-group">
            <label>Skill Level</label>
            <select v-model="newSkill.level" class="form-control">
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="expert">Expert</option>
            </select>
          </div>
          <div class="form-actions">
            <button class="btn btn-secondary" @click="cancelAddSkill">
              Cancel
            </button>
            <button class="btn btn-primary" @click="addNewSkill">
              Add Skill
            </button>
          </div>
        </div>

        <div class="skills-container">
          <div
            v-for="skill in candidateSkills"
            :key="skill.id"
            class="skill-badge"
          >
            {{ getSkillName(skill.skill_id) }} ({{ skill.level }})
            <button class="remove-skill" @click="removeSkill(skill.id)">
              &times;
            </button>
          </div>
        </div>

        <!-- Skills Multiselect -->
        <div class="form-group">
          <label>Select Skills</label>
          <multiselect
            v-model="selectedSkills"
            :options="skillOptions"
            :multiple="true"
            :close-on-select="false"
            :limit="10"
            :show-labels="false"
            placeholder="Search and select skills"
            label="name"
            track-by="id"
            :searchable="true"
          />
        </div>
      </div>

      <!-- Save Button -->
      <div class="form-actions">
        <button
          class="btn btn-primary"
          :disabled="loading"
          @click="saveProfile"
        >
          {{ loading ? "Saving..." : "Save Changes" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { required, email } from "@vuelidate/validators";
import api from "@/services/api.js";
import Multiselect from "vue-multiselect";

export default {
  components: { Multiselect },
  setup() {
    const formData = reactive({
      headline: "",
      experience_years: null,
      linkedin_url: null,
      resume: null,
      resume_path: null,
      user: { name: "", email: "" },
    });

    const rules = {
      user: { name: { required }, email: { required, email } },
    };

    const v$ = useVuelidate(rules, formData);

    const skillOptions = ref([]);
    const candidateSkills = ref([]);
    const selectedSkills = ref([]);
    const showAddSkillForm = ref(false);
    const newSkill = reactive({ name: "", level: "intermediate" });
    const loading = ref(false);

    const userId = JSON.parse(localStorage.getItem("user")).id;

    const fetchData = async () => {
      try {
        const [profileRes, skillsRes, candidateSkillsRes] = await Promise.all([
          api.candidate.getProfile(),
          api.candidate.getSkills(),
          api.candidate.getCandidateSkills(),
        ]);
        Object.assign(formData, profileRes.data);
        skillOptions.value = skillsRes.data;
        candidateSkills.value = candidateSkillsRes.data;
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    const getSkillName = (skillId) => {
      const skill = skillOptions.value.find((s) => s.id === skillId);
      return skill ? skill.name : "Unknown Skill";
    };

    const addNewSkill = async () => {
      try {
        const skillRes = await api.candidate.addSkill(newSkill.name);
        await api.candidate.addCandidateSkill(skillRes.data.id, newSkill.level);
        skillOptions.value.push(skillRes.data);
        candidateSkills.value.push({
          skill_id: skillRes.data.id,
          level: newSkill.level,
        });
        resetAddForm();
      } catch (error) {
        console.error("Error adding skill:", error);
      }
    };

    const cancelAddSkill = () => {
      resetAddForm();
    };

    const resetAddForm = () => {
      showAddSkillForm.value = false;
      newSkill.name = "";
      newSkill.level = "intermediate";
    };

    const removeSkill = async (candidateSkillId) => {
      try {
        candidateSkills.value = candidateSkills.value.filter(
          (s) => s.id !== candidateSkillId
        );
      } catch (error) {
        console.error("Error removing skill:", error);
      }
    };

    const hideResume = () => {};

    const saveProfile = async () => {
      const isValid = await v$.value.$validate();
      if (!isValid) return;
      loading.value = true;
      try {
        const data = {
          headline: formData.headline,
          experience_years: formData.experience_years,
          linkedin_url: formData.linkedin_url,
          user: {
            name: formData.user.name,
            email: formData.user.email,
          },
        };
        await api.candidate.updateProfile(data, userId);
      } catch (error) {
        console.error("Error updating profile:", error);
      } finally {
        loading.value = false;
      }
    };

    onMounted(fetchData);

    return {
      formData,
      v$,
      skillOptions,
      candidateSkills,
      selectedSkills,
      showAddSkillForm,
      newSkill,
      loading,
      getSkillName,
      addNewSkill,
      removeSkill,
      cancelAddSkill,
      saveProfile,
    };
  },
};
</script>

<style scoped>
.settings-card {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.settings-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  border: 1px solid #eee;
  border-radius: 8px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.skills-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.skill-badge {
  background: #f0f2f5;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.remove-skill {
  border: none;
  background: none;
  color: #666;
  cursor: pointer;
  padding: 0 0.25rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.add-skill-form {
  margin-bottom: 1.5rem;
  padding: 1rem;
  border: 1px dashed #ccc;
  border-radius: 8px;
}

.form-actions {
  margin-top: 1rem;
  text-align: right;
}
</style>
